<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Course;
use App\Models\Difficulty_level;
use App\Models\Subcategory;
use App\Models\Instructor;
use App\Models\Addon\Product\Product;
use App\Models\Addon\Product\ProductCategory;
use App\Traits\ApiStatusTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AcademyHomeController extends Controller
{
    use ApiStatusTrait;

    /** Max plain-text length for `excerpt` on GET /api/academy/blogs (full `details` still returned). */
    private const BLOG_LIST_EXCERPT_CHAR_LIMIT = 10000;

    /**
     * GET /api/academy/courses
     *
     * Returns active courses for the academy homepage.
     * Supports: ?featured=1, ?sort=newest|oldest, ?limit=N, ?category_id=N, ?category_name=Name
     */
    public function courses(Request $request)
    {
        try {
            $query = Course::where('status', 1)
                ->where('private_mode', '!=', 1)
                ->publicCatalog();

            if ($request->filled('featured')) {
                $query->where('is_featured', 1);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('category_name')) {
                $name = trim((string) $request->category_name);
                if ($name !== '') {
                    $category = Category::active()
                        ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                        ->first();
                    if ($category) {
                        $query->where('category_id', $category->id);
                    } else {
                        return $this->success([], 'Courses fetched successfully');
                    }
                }
            }

            $sort = $request->input('sort', 'newest');
            $query->orderBy('id', $sort === 'oldest' ? 'ASC' : 'DESC');

            $limit = (int) $request->input('limit', 10);
            $courses = $query->with(['instructor', 'language'])->limit($limit)->get();

            $currencySymbol = function_exists('get_currency_symbol') ? get_currency_symbol() : 'Rs';

            $result = $courses->map(function ($course) use ($currencySymbol) {
                return [
                    'id' => $course->id,
                    'uuid' => $course->uuid,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'subtitle' => $course->subtitle,
                    'short_description' => $course->short_description,
                    'description' => $course->description,
                    'price' => $course->price,
                    'old_price' => $course->old_price,
                    'image_url' => getImageFile($course->image_path),
                    'home_image_1_url' => getImageFile($course->home_image_1_path),
                    'home_image_2_url' => getImageFile($course->home_image_2_path),
                    'duration' => $course->duration,
                    'duration_type' => $course->duration_type,
                    'is_featured' => $course->is_featured,
                    'instructor_name' => $course->instructor?->name ?? null,
                    'category' => $course->category ? [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                        'slug' => $course->category->slug,
                    ] : null,
                    'created_at' => $course->created_at?->toISOString(),
                    /** Align with academy/category-courses cards (CourseCard on Next). */
                    'language_name' => $course->language ? $course->language->name : 'English',
                    'average_rating' => $course->average_rating ?? 0,
                    'classes' => (int) ($course->classes ?? 0),
                    'currency_symbol' => $currencySymbol,
                ];
            });

            return $this->success($result, 'Courses fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/instructors
     *
     * Returns featured, approved instructors (same as homepage "Teachers Who Inspire").
     * Used by cef-online-academy /about/teachers page.
     */
    public function instructors(Request $request)
    {
        try {
            $instructors = Instructor::where('is_featured', 1)
                ->where('status', STATUS_APPROVED)
                ->with('user')
                ->get();

            $result = $instructors->map(function ($instructor) {
                $imageUrl = $instructor->user
                    ? getImageFile($instructor->user->image_path)
                    : null;

                return [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'professional_title' => $instructor->professional_title ?? '',
                    'about_me' => $instructor->about_me ?? '',
                    'image_url' => $imageUrl,
                    'slug' => $instructor->slug ?? '',
                    'profile_url' => url(route('userProfile', ['user' => $instructor->id])),
                ];
            });

            return $this->success($result->values()->all(), 'Instructors fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/blogs
     *
     * Returns active blog posts for the academy homepage.
     * Supports: ?limit=N, ?sort=newest|oldest
     */
    public function blogs(Request $request)
    {
        try {
            $query = Blog::where('status', 1);

            $sort = $request->input('sort', 'newest');
            $query->orderBy('id', $sort === 'oldest' ? 'ASC' : 'DESC');

            $limit = (int) $request->input('limit', 5);
            $blogs = $query->with('category', 'user')->limit($limit)->get();

            $result = $blogs->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'uuid' => $blog->uuid,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'details' => $blog->details,
                    'excerpt' => \Illuminate\Support\Str::limit(strip_tags($blog->details), self::BLOG_LIST_EXCERPT_CHAR_LIMIT),
                    'image_url' => getImageFile($blog->image_path),
                    'author' => $blog->user ? $blog->user->name : null,
                    'category' => $blog->category ? [
                        'id' => $blog->category->id,
                        'name' => $blog->category->name,
                        'slug' => $blog->category->slug,
                    ] : null,
                    'created_at' => $blog->created_at?->toISOString(),
                    'date' => $blog->created_at ? Carbon::parse($blog->created_at)->format('d M Y') : null,
                ];
            });

            return $this->success($result, 'Blogs fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/blog/{slug}
     *
     * Returns a single active blog by slug (for detail page). 404 if not found.
     */
    public function blogBySlug(string $slug)
    {
        try {
            $slug = rawurldecode($slug);
            $blog = Blog::where('status', 1)->where('slug', $slug)->with('category', 'user')->first();
            // If not found, try normalized slug (en-dash/em-dash -> hyphen) for URL compatibility
            if (!$blog) {
                $normalized = str_replace(["\u{2013}", "\u{2014}"], '-', $slug);
                if ($normalized !== $slug) {
                    $blog = Blog::where('status', 1)->where('slug', $normalized)->with('category', 'user')->first();
                }
            }
            if (!$blog) {
                return $this->error([], 'Blog not found', 404);
            }
            $result = [
                'id' => $blog->id,
                'uuid' => $blog->uuid,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'details' => $blog->details,
                'excerpt' => \Illuminate\Support\Str::limit(strip_tags($blog->details), 200),
                'image_url' => getImageFile($blog->image_path),
                'author' => $blog->user ? $blog->user->name : null,
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                    'slug' => $blog->category->slug,
                ] : null,
                'created_at' => $blog->created_at?->toISOString(),
                'date' => $blog->created_at ? Carbon::parse($blog->created_at)->format('d M Y') : null,
            ];
            return $this->success($result, 'Blog fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/products
     *
     * Returns active products for the academy homepage bookshop section.
     * Supports: ?featured=1, ?limit=N, ?sort=newest|oldest
     */
    public function products(Request $request)
    {
        try {
            $query = Product::where('status', 1);

            if ($request->filled('featured')) {
                $query->where('is_feature', 1);
            }

            $sort = $request->input('sort', 'newest');
            $query->orderBy('id', $sort === 'oldest' ? 'ASC' : 'DESC');

            $limit = (int) $request->input('limit', 10);
            $products = $query->with('category')->limit($limit)->get();

            $result = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'current_price' => $product->current_price,
                    'old_price' => $product->old_price,
                    'thumbnail_url' => getImageFile($product->thumbnail_path),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'average_review' => $product->average_review,
                    'discount_percentage' => $product->discount_percentage,
                    'quantity' => $product->quantity,
                    'created_at' => $product->created_at?->toISOString(),
                ];
            });

            return $this->success($result, 'Products fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/product-categories
     *
     * Returns product categories for sidebar and category sections.
     */
    public function productCategories(Request $request)
    {
        try {
            $categories = ProductCategory::orderBy('id')->get();
            $result = $categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'is_feature' => $cat->is_feature ?? null,
                ];
            });
            return $this->success($result, 'Categories fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/products/list
     *
     * Filtered product list with pagination.
     * Params: category_id, min_price, max_price, rating, discount, search_key|q, sort (newest|oldest|price_asc|price_desc), page, per_page.
     */
    public function productList(Request $request)
    {
        try {
            $query = Product::where('status', 1)->with('category');

            if ($request->filled('category_id')) {
                $query->where('product_category_id', $request->category_id);
            }
            if ($request->has('min_price') && $request->min_price !== '' && $request->min_price !== null) {
                $min = (float) $request->min_price;
                if ($min >= 0) {
                    $query->where('current_price', '>=', $min);
                }
            }
            if ($request->has('max_price') && $request->max_price !== '' && $request->max_price !== null) {
                $max = (float) $request->max_price;
                if ($max >= 0) {
                    $query->where('current_price', '<=', $max);
                }
            }
            if ($request->filled('rating') && $request->rating !== '') {
                $query->where('average_review', '>=', (float) $request->rating);
            }
            if ($request->filled('discount') && $request->discount !== '') {
                $query->where('discount_percentage', '>=', (float) $request->discount);
            }
            $searchKey = $request->input('search_key') ?: $request->input('q');
            if ($searchKey !== null && $searchKey !== '') {
                $query->where('title', 'LIKE', '%' . $searchKey . '%');
            }
            if ($request->filled('tag')) {
                $query->join('tag_products', 'tag_products.product_id', '=', 'products.id')
                    ->where('tag_products.product_tag_id', $request->tag)
                    ->groupBy('products.id')
                    ->select('products.*');
            }

            $sort = $request->input('sort', 'newest');
            if ($sort === 'oldest') {
                $query->orderBy('id', 'ASC');
            } elseif ($sort === 'price_asc') {
                $query->orderBy('current_price', 'ASC');
            } elseif ($sort === 'price_desc') {
                $query->orderBy('current_price', 'DESC');
            } else {
                $query->orderBy('id', 'DESC');
            }

            $perPage = (int) $request->input('per_page', 9);
            $perPage = $perPage >= 1 && $perPage <= 100 ? $perPage : 9;
            $paginator = $query->paginate($perPage);

            $data = $paginator->getCollection()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'current_price' => $product->current_price,
                    'old_price' => $product->old_price,
                    'thumbnail_url' => getImageFile($product->thumbnail_path),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'average_review' => $product->average_review,
                    'discount_percentage' => $product->discount_percentage,
                    'quantity' => $product->quantity,
                    'created_at' => $product->created_at?->toISOString(),
                ];
            });

            return $this->success([
                'data' => $data,
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
            ], 'Product list fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/search
     *
     * Searches courses and blogs.
     * Supports: ?q=searchterm, ?type=courses|blogs|all, ?limit=N
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $type = $request->input('type', 'all');
            $limit = (int) $request->input('limit', 10);

            if (empty($query)) {
                return $this->success(['courses' => [], 'blogs' => []], 'No search query provided');
            }

            $results = [];

            if ($type === 'all' || $type === 'courses') {
                $courses = Course::where('status', 1)
                    ->where('private_mode', '!=', 1)
                    ->publicCatalog()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                            ->orWhere('subtitle', 'LIKE', "%{$query}%")
                            ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('id', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->map(function ($course) {
                        return [
                            'id' => $course->id,
                            'type' => 'course',
                            'title' => $course->title,
                            'slug' => $course->slug,
                            'description' => $course->short_description ?? \Illuminate\Support\Str::limit(strip_tags($course->description), 150),
                            'image_url' => getImageFile($course->image_path),
                            'url' => '/courses/' . $course->slug,
                        ];
                    });

                $results['courses'] = $courses;
            }

            if ($type === 'all' || $type === 'blogs') {
                $blogs = Blog::where('status', 1)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                            ->orWhere('details', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('id', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->map(function ($blog) {
                        return [
                            'id' => $blog->id,
                            'type' => 'blog',
                            'title' => $blog->title,
                            'slug' => $blog->slug,
                            'description' => \Illuminate\Support\Str::limit(strip_tags($blog->details), 150),
                            'image_url' => getImageFile($blog->image_path),
                            'url' => '/blogs/' . $blog->slug,
                        ];
                    });

                $results['blogs'] = $blogs;
            }

            return $this->success($results, 'Search results');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/categories
     *
     * Returns active categories (id, name, slug) for section-wise listing.
     * Query param: type (optional) – 1 = Quran Tutoring, 2 = Others. Default returns all.
     */
    public function categories(Request $request)
    {
        try {
            $query = Category::active()->orderBy('sort_order')->orderBy('name');
            $type = $request->input('type');
            if (in_array($type, ['1', '2'], true)) {
                $query->where('category_type', (int) $type);
            }
            $categories = $query->get()->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'sort_order' => (int) ($c->sort_order ?? 0),
                ];
            });
            return $this->success($categories, 'Categories fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/subcategories
     *
     * Returns subcategories (id, name, slug, category_id, category_slug) for categories of the given type.
     * Query param: type (optional) – 1 = Quran Tutoring, 2 = Others. Default returns all.
     */
    public function subcategories(Request $request)
    {
        try {
            $type = $request->input('type');
            $query = Subcategory::query()
                ->select('subcategories.*')
                ->join('categories', 'categories.id', '=', 'subcategories.category_id')
                ->where('categories.status', 1);

            if (in_array($type, ['1', '2'], true)) {
                $query->where('categories.category_type', (int) $type);
            }

            $query->with('category:id,name,slug,category_type,sort_order')
                ->orderBy('categories.sort_order')
                ->orderBy('categories.id')
                ->orderBy('subcategories.sort_order')
                ->orderBy('subcategories.name');

            $subcategories = $query->get()->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'slug' => $sub->slug,
                    'category_id' => $sub->category_id,
                    'category_slug' => $sub->category ? $sub->category->slug : null,
                    'sort_order' => (int) $sub->sort_order,
                ];
            });

            return $this->success($subcategories, 'Subcategories fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }

    /**
     * GET /api/academy/category-courses
     *
     * Returns category courses with filters (same behaviour as cef-backend category/courses page).
     * Query params: category_slug (required), keyword, sortBy_id (1=All,2=Newest,3=Oldest),
     * categoryId, difficultyLevelId, ratingId, learnerAccessibilityType (free|paid), durationId (1-4),
     * limit (optional, for preview e.g. 4), page & per_page (optional, for pagination).
     */
    public function categoryCourses(Request $request)
    {
        try {
            $categorySlug = $request->input('category_slug');
            if (empty($categorySlug)) {
                return $this->validationError([], 'category_slug is required');
            }

            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                return $this->failed([], 'Category not found');
            }

            $subcategorySlug = $request->input('subcategory_slug');
            $subcategory = null;
            if (!empty($subcategorySlug)) {
                $subcategory = Subcategory::where('slug', $subcategorySlug)->where('category_id', $category->id)->first();
                if (!$subcategory) {
                    return $this->failed([], 'Subcategory not found');
                }
            }

            $categoryIdFilter = $request->input('categoryId');
            if ($subcategory) {
                $query = Course::where('private_mode', '!=', 1)->publicCatalog()->where('subcategory_id', $subcategory->id)->active();
            } elseif ($categoryIdFilter) {
                $query = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($categoryIdFilter)->active();
            } else {
                $query = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($category->id)->active();
            }

            if ($request->filled('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            $difficultyLevelId = $request->input('difficultyLevelId');
            $ratingId = $request->input('ratingId');
            $learnerAccessibilityType = $request->input('learnerAccessibilityType');
            $durationId = $request->input('durationId');

            $query->where(function ($q) use ($difficultyLevelId, $ratingId, $learnerAccessibilityType) {
                if ($difficultyLevelId) {
                    $q->where('difficulty_level_id', $difficultyLevelId);
                }
                if ($ratingId !== null && $ratingId !== '') {
                    $q->where('average_rating', '>=', (int) $ratingId);
                }
                if ($learnerAccessibilityType) {
                    if ($learnerAccessibilityType === 'free') {
                        $q->where('price', 0);
                    } else {
                        $q->where('price', '>', 0);
                    }
                    $q->where('learner_accessibility', $learnerAccessibilityType);
                }
            });

            if ($durationId) {
                $courses = $query->get();
                $courseIds = $courses->filter(function ($course) use ($durationId) {
                    $duration = $course->duration_in_hours;
                    if (!$duration) {
                        return false;
                    }
                    if ($durationId == 1) {
                        return $duration <= 24;
                    }
                    if ($durationId == 2) {
                        return $duration >= 24 && $duration <= 36;
                    }
                    if ($durationId == 3) {
                        return $duration >= 36 && $duration <= 72;
                    }
                    if ($durationId == 4) {
                        return $duration >= 72;
                    }
                    return false;
                })->pluck('id');
                $query = Course::where('private_mode', '!=', 1)->publicCatalog()->whereIn('id', $courseIds)->active();
            }

            $sortBy = (int) $request->input('sortBy_id', 1);
            if ($sortBy == 2) {
                $query->orderBy('id', 'DESC');
            } elseif ($sortBy == 3) {
                $query->orderBy('id', 'ASC');
            }

            $limit = (int) $request->input('limit');
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 9);
            if ($perPage < 1) {
                $perPage = 9;
            }
            if ($perPage > 100) {
                $perPage = 100;
            }

            if ($limit > 0) {
                $courses = $query->with('language')->limit($limit)->get();
                $total = $courses->count();
                $paginator = null;
            } else {
                $paginator = $query->with('language')->paginate($perPage, ['*'], 'page', $page);
                $courses = $paginator->getCollection();
                $total = $paginator->total();
            }

            $currencySymbol = function_exists('get_currency_symbol') ? get_currency_symbol() : 'Rs';

            $coursesData = $courses->map(function ($course) use ($currencySymbol) {
                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'subtitle' => $course->subtitle ?? $course->short_description ?? '',
                    'language_name' => $course->language ? $course->language->name : 'English',
                    'average_rating' => $course->average_rating ?? 0,
                    'image_url' => getImageFile($course->image_path),
                    'price' => (float) $course->price,
                    'classes' => (int) ($course->classes ?? 0),
                    'currency_symbol' => $currencySymbol,
                ];
            });

            $categories = Category::with('subcategories')->active()->get()->map(function ($c) {
                return ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug];
            });

            $difficultyLevels = Difficulty_level::all()->map(function ($d) {
                return ['id' => $d->id, 'name' => $d->name];
            });

            $payload = [
                'category' => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug],
                'categories' => $categories,
                'difficulty_levels' => $difficultyLevels,
                'courses' => $coursesData,
            ];

            if ($paginator) {
                $payload['pagination'] = [
                    'total' => $paginator->total(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                ];
            }

            return $this->success($payload, 'Category courses fetched successfully');
        } catch (\Exception $e) {
            return $this->failed([], $e->getMessage());
        }
    }
}
