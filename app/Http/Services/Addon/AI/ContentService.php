<?php

namespace App\Http\Services\Addon\AI;

use App\Models\Addon\AI\GenerateContent;
use App\Models\SearchResult;
use App\Models\SearchResultItem;
use App\Traits\ApiStatusTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Orhanerday\OpenAi\OpenAi;


class ContentService
{
    use ApiStatusTrait;

    public function generate($request)
    {
        DB::beginTransaction();
        try {

            if ((int)$request->ulc > env('MAX_API_REQUEST', 9999999)) {
                throw new Exception('Your free content generate limit is over');
            }

            $service = openAIService($request->service);
            $keywords = $request->keywords;
            $creativity = $request->creativity_level;
            $output = $request->output;
            $language = $request->language;

            $prompt = $service->prompt;
            $prompt = str_replace("#keywords#", $keywords, $prompt);
            $prompt = str_replace("#language#", $language, $prompt);
        
            $maxToken = get_option('open_ai_max_token', 10);
            $model = get_option('open_ai_model','gpt-3.5-turbo');

            $client = new OpenAi(get_option('chat_gpt_api'));
            $complete = [];
            if($service->is_image == 1){
                $complete = $client->image([
                    "prompt" => $prompt,
                    "n" => (int)$output,
                    "size" => "1024x1024",
                    "response_format" => "url",
                ]);
            }
            else if ($model == 'gpt-3.5-turbo' || $model == 'gpt-4' || $model == 'gpt-4-32k') {
                $complete = $client->chat([
                    'model' => $model,
                    "temperature" => (float)$creativity,
                    'max_tokens' => (int)$maxToken ?? 1,
                    'n' => (int)$output,
                    'messages' => [
                        ["role" => "user", "content" => $prompt],
                    ],
                ]);
            }else{
                $complete = $client->completion([
                    "model" => $model,
                    "temperature" => (float)$creativity,
                    'max_tokens' => (int)$maxToken ?? 1,
                    'prompt' => sprintf($prompt),
                    'n' => (int)$output,
                ]);
            }

            $result = json_decode($complete , true);
            if(isset($result['data'])){
                $getApiResponseImage = $result['data']; // if n=2 then choices become 2
                $generatedOutputs = [];
                foreach ($getApiResponseImage as $choice) {
                    $outputImage = $choice['url'];
                    $generatedOutputs[] = $outputImage;
                }

                GenerateContent::create([
                    'user_id' => auth()->id(),
                    'service' => $request->service,
                    'is_image' => 1,
                    'keywords' => $keywords,
                    'creativity' => $creativity,
                    'variation' => $output,
                    'language' => $language,
                    'prompt' => $prompt,
                    'output' => json_encode($generatedOutputs),
                    'token' => count($generatedOutputs),
                ]);

                $data['view'] = view('addon.AI.image_outputs', ['generatedOutputs' => $generatedOutputs])->render();

                DB::commit();
                $message = __('Generated Successfully');
                return $this->success($data, $message);
            }
            else if (isset($result['choices'])) {
                $getApiResponseText = $result['choices']; // if n=2 then choices become 2
                $generatedOutputs = [];
                foreach ($getApiResponseText as $choice) {
                    if ($model == 'gpt-3.5-turbo' || $model == 'gpt-4' || $model == 'gpt-4-32k') {
                        $outputContent = $choice['message']['content'];
                     
                    }else{
                        $outputContent = $choice['text'];
                    }

                    $outputContent = ltrim($outputContent, "\r\n");
                    $outputContent = ltrim($outputContent, "\r");
                    $outputContent = ltrim($outputContent, "\n");
                    $generatedOutputs[] = $outputContent;
                }

                GenerateContent::create([
                    'user_id' => auth()->id(),
                    'service' => $request->service,
                    'keywords' => $keywords,
                    'creativity' => $creativity,
                    'variation' => $output,
                    'language' => $language,
                    'prompt' => $prompt,
                    'output' => json_encode($generatedOutputs),
                    'token' => $result['usage']['total_tokens'],
                ]);

                $data['view'] = view('addon.AI.content_outputs', ['generatedOutputs' => $generatedOutputs])->render();

                DB::commit();
                $message = __('Generated Successfully');
                return $this->success($data, $message);
            }else{
                if (isset($result['error']['message']) && $result['error']['message'] != "") {
                    $message = $result['error']['message'];
                } else {
                    $message = __('There is an issue with your openai api');
                }
                throw new Exception($message);

            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return $this->error([],  $message);
        }
    }

    public function getAll()
    {
        $contents = GenerateContent::join('users', 'users.id', '=', 'generate_contents.user_id')
            ->join('open_a_i_prompts', 'open_a_i_prompts.id', '=', 'generate_contents.service')
            ->leftJoin('instructors as ins', 'ins.user_id', '=', 'users.id')
            ->leftJoin('organizations as org', 'org.user_id', '=', 'users.id')
            ->select('generate_contents.id',
            'generate_contents.user_id',
            'generate_contents.language',
            'generate_contents.token',
            'generate_contents.is_image',
            'generate_contents.service',
            'generate_contents.keywords',
            'generate_contents.variation',
            'open_a_i_prompts.category',
            'users.name',
            'ins.uuid as instructor_uuid',
            'org.uuid as organization_uuid')
            ->orderBy('generate_contents.id', 'DESC')
            ->paginate(20);
        return $contents;
    }

    public function getInfo($id){
        return GenerateContent::find($id);
    }
}
