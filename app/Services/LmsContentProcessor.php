<?php

namespace App\Services;

use DOMDocument;
use Sabberworm\CSS\Parser as CssParser;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\CSSList\AtRuleBlockList;
use HTMLPurifier, HTMLPurifier_Config;

class LmsContentProcessor
{
    protected string $scope = ':where(.lms-scope)';

    public function process(string $rawHtml): string
    {
        [$innerHtml, $rawCss] = $this->extractHtmlAndCss($rawHtml);
        $innerHtml = $this->purify($innerHtml);
        $scopedCss = $this->scopeCss($rawCss);

        return '<div class="lms-scope">'
            . ($scopedCss ? '<style>' . $scopedCss . '</style>' : '')
            . $innerHtml
            . '</div>';
    }

    protected function extractHtmlAndCss(string $raw): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'
            . '<div id="__wrap">' . $raw . '</div>'
        );
        $xp = new \DOMXPath($dom);

        // collect and remove <style>
        $css = [];
        foreach ($xp->query('//style') as $style) {
            $css[] = $style->nodeValue ?? '';
            $style->parentNode->removeChild($style);
        }

        // kill scripts + external stylesheets
        foreach ($xp->query('//script|//link[@rel="stylesheet"]') as $n) {
            $n->parentNode->removeChild($n);
        }

        // unwrap html/head/body
        foreach ($xp->query('//html|//head|//body') as $n) {
            while ($n->firstChild) {
                $n->parentNode->insertBefore($n->firstChild, $n);
            }
            $n->parentNode->removeChild($n);
        }

        // drop inline JS handlers
        foreach ($xp->query('//*[@*]') as $el) {
            $remove = [];
            foreach (iterator_to_array($el->attributes) as $attr) {
                if (stripos($attr->name, 'on') === 0) {
                    $remove[] = $attr->name;
                }
            }
            foreach ($remove as $a) {
                $el->removeAttribute($a);
            }
        }

        // inner HTML
        $wrap = $dom->getElementById('__wrap');
        $inner = '';
        foreach ($wrap->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }

        return [$inner, trim(implode("\n", $css))];
    }

    protected function purify(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('HTML.DefinitionID', 'html5-definitions');
        $config->set('HTML.DefinitionRev', 1);
        $config->set('Cache.DefinitionImpl', null); // disable cache during dev

        // Add HTML5 elements BEFORE purifier instance creation
        if ($def = $config->maybeGetRawHTMLDefinition(true)) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('article', 'Block', 'Flow', 'Common');
            $def->addElement('aside', 'Block', 'Flow', 'Common');
            $def->addElement('nav', 'Block', 'Flow', 'Common');
            $def->addElement('header', 'Block', 'Flow', 'Common');
            $def->addElement('footer', 'Block', 'Flow', 'Common');
            $def->addElement('main', 'Block', 'Flow', 'Common');
            $def->addElement('figure', 'Block', 'Optional:figcaption', 'Common');
            $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
            $def->addElement('time', 'Inline', 'Inline', 'Common');
            $def->addElement('mark', 'Inline', 'Inline', 'Common');

            $def->addElement(
                'video',
                'Block',
                'Flow',
                'Common',
                [
                    'src' => 'URI',
                    'controls' => 'Bool',
                    'autoplay' => 'Bool',
                    'loop' => 'Bool',
                    'muted' => 'Bool',
                    'poster' => 'URI',
                    'preload' => 'Enum#auto,metadata,none'
                ]
            );
            $def->addElement(
                'audio',
                'Block',
                'Flow',
                'Common',
                [
                    'src' => 'URI',
                    'controls' => 'Bool',
                    'autoplay' => 'Bool',
                    'loop' => 'Bool',
                    'muted' => 'Bool',
                    'preload' => 'Enum#auto,metadata,none'
                ]
            );
            $def->addElement(
                'source',
                'Block',
                'Empty',
                null,
                ['src' => 'URI', 'type' => 'Text']
            );
        }

        // Now create purifier and purify
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }

    protected function scopeCss(string $css): string
    {
        if ($css === '') {
            return '';
        }

        $doc = (new CssParser($css))->parse();
        $this->prefixCssList($doc);

        return $doc->render(OutputFormat::createCompact());
    }

    protected function prefixCssList($list): void
    {
        foreach ($list->getContents() as $node) {
            if ($node instanceof DeclarationBlock) {
                $selectors = $node->getSelectors();
                foreach ($selectors as $s) {
                    $sel = trim($s->getSelector());

                    if (preg_match('/^:root\b/i', $sel)) {
                        $s->setSelector($this->scope);
                        continue;
                    }

                    $sel = preg_replace('/^(html|body)\b/i', '', $sel);
                    $sel = trim($sel) ?: '*';

                    $s->setSelector($this->scope . ' ' . $sel);
                }
                $node->setSelectors($selectors);
            } elseif ($node instanceof AtRuleBlockList) {
                $this->prefixCssList($node);
            }
        }
    }
}