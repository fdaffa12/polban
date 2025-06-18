<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\Event;
use App\Models\Article;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\LenteraRestorasiVision;
use App\Models\LenteraRestorasiMission;
use App\Models\LenteraRestorasiCoreValue;
use App\Models\LenteraRestorasiImage;
use App\Models\LenteraRestorasiContent;

class HomeController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first();
        $today = now();

        // Coba ambil event yang akan datang (upcoming)
        $upcomingEvent = Event::with(['dates', 'department'])
            ->whereHas('dates', function ($query) use ($today) {
                $query->where('event_date', '>', $today->toDateString());
            })
            ->orderBy(function ($query) {
                // Order by earliest event date
                $query->select(DB::raw('MIN(event_date)'))
                    ->from('event_dates')
                    ->whereColumn('event_dates.event_id', 'events.id');
            })
            ->first();

        // Jika tidak ada upcoming event, ambil event terakhir berdasarkan event_date
        if (!$upcomingEvent) {
            $upcomingEvent = Event::with(['dates', 'department'])
                ->whereHas('dates')
                ->orderBy(function ($query) {
                    // Order by latest event date
                    $query->select(DB::raw('MAX(event_date)'))
                        ->from('event_dates')
                        ->whereColumn('event_dates.event_id', 'events.id');
                }, 'desc')
                ->first();
        }

        // Map event jika ada
        $featuredEvent = null;
        if ($upcomingEvent) {
            $eventDates = $upcomingEvent->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();

            $featuredEvent = [
                'id' => $upcomingEvent->id,
                'event_name' => $upcomingEvent->event_name,
                'event_detail' => Str::limit(strip_tags($upcomingEvent->event_detail), 150),
                'fee_type' => $upcomingEvent->fee_type,
                'event_flyer' => $upcomingEvent->event_flyer ? "/storage/{$upcomingEvent->event_flyer}" : null,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'department' => [
                    'id' => $upcomingEvent->department->id,
                    'name' => $upcomingEvent->department->dept_name
                ],
                'is_upcoming' => $firstDate->event_date > $today->toDateString()
            ];
        }

        // Ambil event lainnya seperti sebelumnya
        $activeAndUpcomingEvents = Event::with('dates')
            ->whereHas('dates', function ($query) use ($today) {
                $query->where('event_date', '>=', $today->toDateString());
            })
            ->orderBy(function ($query) use ($today) {
                // Custom ordering untuk memprioritaskan event yang sedang berjalan
                $query->select(DB::raw('
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM event_dates 
                            WHERE event_dates.event_id = events.id 
                            AND event_dates.event_date = "' . $today->toDateString() . '"
                        ) THEN 1
                        ELSE 2
                    END
                '));
            })
            ->take(3) // Ambil 3 event aktif/upcoming
            ->get();

        // Jika slot masih tersisa, ambil event yang sudah selesai
        $remainingSlots = 5 - $activeAndUpcomingEvents->count();
        $pastEvents = collect();

        if ($remainingSlots > 0) {
            $pastEvents = Event::with('dates')
                ->whereHas('dates', function ($query) use ($today) {
                    $query->where('event_date', '<', $today->toDateString());
                })
                ->latest()
                ->take($remainingSlots)
                ->get();
        }

        // Gabungkan dan map events
        $events = $activeAndUpcomingEvents->concat($pastEvents);

        $mappedEvents = $events->map(function ($event) use ($today) {
            $eventDates = $event->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();

            // Determine status based on event dates
            $status = 'upcoming';
            if ($firstDate->event_date <= $today->toDateString() && $lastDate->event_date >= $today->toDateString()) {
                $status = 'ongoing';
            } elseif ($lastDate->event_date < $today->toDateString()) {
                $status = 'past';
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 150),
                'fee_type' => $event->fee_type,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'dates' => $event->dates,
                'status' => $status,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'department' => [
                    'id' => $event->department->id,
                    'name' => $event->department->dept_name
                ]
            ];
        });

        // Ambil kategori News
        $newsCategory = Category::where('name', 'News')->first();

        // Ambil artikel dengan kategori News
        $newsArticles = [];
        if ($newsCategory) {
            $newsArticles = Article::with(['category', 'user'])
                ->where('category_id', $newsCategory->id)
                ->where('status', 'publish')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'content' => Str::limit(strip_tags($article->content), 150),
                        'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                        'created_at' => $article->created_at->format('d M Y'),
                        'author' => $article->user->name,
                    ];
                });
        }

        // Fetch departments
        $departments = Department::all()->map(function ($department) {
            return [
                'id' => $department->id,
                'dept_name' => $department->dept_name,
                'image' => $department->image ? "/storage/{$department->image}" : null,
            ];
        });

        return Inertia::render('Home', [
            'canLogin' => true,
            'canRegister' => true,
            'aboutUs' => [
                'title' => $aboutUs->au_title ?? '',
                'description' => $aboutUs->au_desc ? strip_tags($aboutUs->au_desc) : '',
                'image' => $aboutUs->au_image ? "/storage/{$aboutUs->au_image}" : null,
            ],
            'featuredEvent' => $featuredEvent,
            'events' => $mappedEvents,
            'newsArticles' => $newsArticles,
            'departments' => $departments,
        ]);
    }

    public function about()
    {
        $aboutUs = AboutUs::first();

        return Inertia::render('AboutUs', [
            'about' => [
                'title' => $aboutUs->au_title ?? 'KABINET RUANG OPTIMA',
                'au_desc' => $aboutUs->au_desc,
                'image' => $aboutUs->au_image ? asset('storage/' . $aboutUs->au_image) : null,
                'au_multiple_image' => $aboutUs->au_multiple_image
                    ? array_map(function ($image) {
                        return asset('storage/' . $image);
                    }, $aboutUs->au_multiple_image)
                    : [],
                'au_values' => $aboutUs->au_values,
                'history' => $aboutUs->history,
            ]
        ]);
    }

    public function lenteraRestorasi()
    {
        $visions = LenteraRestorasiVision::all();
        $missions = LenteraRestorasiMission::all();
        $coreValues = LenteraRestorasiCoreValue::all();
        $images = LenteraRestorasiImage::all();
        $content = LenteraRestorasiContent::first();

        return Inertia::render('LenteraRestorasi', [
            'content' => [
                'title' => 'KABINET LENTERA RESTORASI',
                'description' => $content ? nl2br($content->description) : '',
                'visions' => $visions->map(function ($vision) {
                    return [
                        'id' => $vision->id,
                        'content' => nl2br($vision->vision)
                    ];
                }),
                'missions' => $missions->map(function ($mission, $index) {
                    return [
                        'id' => $mission->id,
                        'content' => nl2br($mission->mission)
                    ];
                }),
                'coreValues' => $coreValues->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'title' => $value->title,
                        'description' => nl2br($value->description)
                    ];
                }),
                'images' => $images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'title' => $image->title,
                        'image' => $image->image ? asset('storage/' . $image->image) : null
                    ];
                }),
                'au_multiple_image' => collect($images)->pluck('image')->map(function ($image) {
                    return asset('storage/' . $image);
                })->toArray()
            ]
        ]);
    }

    public function department()
    {
        $departments = Department::with([
            'members' => function ($query) {
                $query->orderBy('position');
            }
        ])->get()->map(function ($department) {
            return [
                'id' => $department->id,
                'dept_name' => $department->dept_name,
                'image' => $department->image ? "/storage/{$department->image}" : null,
                // Perbaiki mapping untuk board_members
                'board_members' => $department->members
                    ->where('job_type', 'board_of_dept')
                    ->values()
                    ->map(fn($member) => $this->mapMember($member))
                    ->toArray(), // Tambahkan toArray()
                // Perbaiki mapping untuk section_heads  
                'section_heads' => $department->members
                    ->where('job_type', 'section_head_dept')
                    ->values()
                    ->map(fn($member) => $this->mapMember($member))
                    ->toArray(), // Tambahkan toArray()
                // Perbaiki mapping untuk staff_members
                'staff_members' => $department->members
                    ->where('job_type', 'staff')
                    ->values()
                    ->map(fn($member) => $this->mapMember($member))
                    ->toArray() // Tambahkan toArray()
            ];
        });

        return Inertia::render('Department', [
            'departments' => $departments
        ]);
    }

    private function mapMember($member)
    {
        return [
            'id' => $member->id,
            'name' => $member->name,
            'image' => $member->image ? "/storage/{$member->image}" : null,
            'job_type' => $member->job_type,
            'job_titles' => $member->job_titles,
            'position' => $member->position
        ];
    }

    public function news()
    {
        $categories = Category::withCount([
            'articles' => function ($query) {
                $query->where('status', 'publish');
            }
        ])->get();

        $articles = Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->latest()
            ->paginate(12);

        $mappedArticles = $articles->getCollection()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'content' => Str::limit(strip_tags($article->content), 200),
                'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                'created_at' => $article->created_at->format('d M Y'),
                'author' => $article->user->name,
                'category' => [
                    'id' => $article->category->id,
                    'name' => $article->category->name
                ]
            ];
        });

        return Inertia::render('News', [
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'articles_count' => $category->articles_count
                ];
            }),
            'articles' => $mappedArticles,
            'popularPosts' => $this->getPopularPosts(), // Add this line
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'has_more_pages' => $articles->hasMorePages()
            ]
        ]);
    }

    public function newsByCategory($categoryId)
    {
        $categories = Category::withCount([
            'articles' => function ($query) {
                $query->where('status', 'publish');
            }
        ])->get();

        $articles = Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->where('category_id', $categoryId)
            ->latest()
            ->paginate(12);

        $mappedArticles = $articles->getCollection()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'content' => Str::limit(strip_tags($article->content), 200),
                'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                'created_at' => $article->created_at->format('d M Y'),
                'author' => $article->user->name,
                'category' => [
                    'id' => $article->category->id,
                    'name' => $article->category->name
                ]
            ];
        });

        return Inertia::render('News', [
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'articles_count' => $category->articles_count
                ];
            }),
            'articles' => $mappedArticles,
            'activeCategory' => $categoryId,
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'has_more_pages' => $articles->hasMorePages()
            ]
        ]);
    }

    public function articleDetail($id)
    {
        $article = Article::with(['category', 'user', 'tags'])
            ->where('status', 'publish')
            ->findOrFail($id);

        // Update view counter
        DB::table('articles')
            ->where('id', $id)
            ->increment('viewed', 1, [
                'updated_at' => now()
            ]);

        // Get updated view count
        $viewCount = $article->fresh()->viewed;

        // Get related articles from same category
        $relatedArticles = Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($relatedArticle) {
                return [
                    'id' => $relatedArticle->id,
                    'title' => $relatedArticle->title,
                    'content' => Str::limit(strip_tags($relatedArticle->content), 150),
                    'featured_image' => $relatedArticle->featured_image ? "/storage/{$relatedArticle->featured_image}" : null,
                    'created_at' => $relatedArticle->created_at->format('d M Y'),
                    'author' => $relatedArticle->user->name,
                ];
            });

        return Inertia::render('ArticleDetail', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                'created_at' => $article->created_at->format('d M Y'),
                'author' => $article->user->name,
                'viewed' => $viewCount, // Add view count to response
                'category' => [
                    'id' => $article->category->id,
                    'name' => $article->category->name
                ],
                'tags' => $article->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                })
            ],
            'relatedArticles' => $relatedArticles
        ]);
    }

    // Add new method for popular posts
    private function getPopularPosts()
    {
        return Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->orderBy('viewed', 'desc')
            ->take(5)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => Str::limit(strip_tags($article->content), 150),
                    'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                    'created_at' => $article->created_at->format('d M Y'),
                    'category' => [
                        'id' => $article->category->id,
                        'name' => $article->category->name
                    ],
                    'viewed' => $article->viewed
                ];
            });
    }
}