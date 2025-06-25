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
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Himpunan;
use App\Models\DeptLogo;

class HomeController extends Controller
{
    public function __construct()
    {
        // Share settings data to all views
        $settings = Setting::first();
        if ($settings) {
            $aboutUs = AboutUs::first();
            $settings->about_us_excerpt = $aboutUs ? Str::limit(strip_tags($aboutUs->au_desc), 500) : '';
            Inertia::share('settings', $settings);
        }
    }

    public function index()
    {
        $aboutUs = AboutUs::first();
        $himpunan = Himpunan::first();
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

        // Hapus getCollection() karena $events sudah merupakan Collection
        $mappedEvents = $events->map(function ($event) {
            $today = now();
            $eventDates = $event->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();

            // Default status adalah past
            $status = 'past';

            if ($firstDate && $lastDate) {
                \Log::info('Event: ' . $event->event_name);
                \Log::info('Today: ' . $today->toDateString());

                // Jika tanggal hari ini sama dengan salah satu tanggal event
                foreach ($eventDates as $date) {
                    // Ubah format tanggal event menjadi Y-m-d saja
                    $eventDate = Carbon::parse($date->event_date)->toDateString();
                    \Log::info('Checking date: ' . $eventDate);
                    \Log::info('Compare with today: ' . ($eventDate === $today->toDateString() ? 'true' : 'false'));

                    if ($eventDate === $today->toDateString()) {
                        $status = 'ongoing';
                        \Log::info('Status set to ongoing');
                        break;
                    }
                }

                // Jika bukan ongoing, cek apakah upcoming
                if ($status !== 'ongoing') {
                    $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                    \Log::info('First date: ' . $firstEventDate);
                    \Log::info('Compare with today: ' . ($firstEventDate > $today->toDateString() ? 'true' : 'false'));

                    if ($firstEventDate > $today->toDateString()) {
                        $status = 'upcoming';
                        \Log::info('Status set to upcoming');
                    }
                }

                \Log::info('Final status: ' . $status);
                \Log::info('------------------------');
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 200),
                'fee_type' => $event->fee_type,
                'fee_amount' => $event->fee_amount,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'status' => $status,
                'department' => [
                    'id' => $event->department->id,
                    'name' => $event->department->dept_name
                ]
            ];
        });

        // Ambil kategori News
        $newsCategory = Category::where('name', 'News')->first();

        // Ambil artikel dengan kategori News
        $newsArticles = Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
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

        // Fetch departments
        $departments = Department::all()->map(function ($department) {
            return [
                'id' => $department->id,
                'dept_name' => $department->dept_name,
                'image' => $department->image ? "/storage/{$department->image}" : null,
            ];
        });

        $deptLogos = DeptLogo::all()->map(function ($deptLogo) {
            return [
                'id' => $deptLogo->id,
                'title' => $deptLogo->title,
                'image' => $deptLogo->image ? "/storage/{$deptLogo->image}" : null,
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
            'himpunan' => $himpunan ? [
                'name' => $himpunan->name,
                'description' => strip_tags($himpunan->description),
                'logo' => $himpunan->logo ? "/storage/{$himpunan->logo}" : null,
                'yt_link' => $himpunan->yt_link,
            ] : null,
            'featuredEvent' => $featuredEvent,
            'events' => $mappedEvents,
            'newsArticles' => $newsArticles,
            'departments' => $departments,
            'deptLogos' => $deptLogos,
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
        $himpunan = Himpunan::first();

        return Inertia::render('LenteraRestorasi', [
            'content' => [
                'title' => 'KABINET LENTERA RESTORASI',
                'description' => $content ? nl2br($content->description) : '',
                'visions' => $visions->map(function ($vision) {
                    return [
                        'id' => $vision->id,
                        'type' => $vision->type,
                        'content' => $vision->type === 'caption' ? nl2br($vision->vision) : null,
                        'image' => $vision->type === 'image' ? asset('storage/' . $vision->image) : null
                    ];
                }),
                'missions' => $missions->map(function ($mission) {
                    return [
                        'id' => $mission->id,
                        'type' => $mission->type,
                        'content' => $mission->type === 'caption' ? nl2br($mission->mission) : null,
                        'image' => $mission->type === 'image' ? asset('storage/' . $mission->image) : null
                    ];
                }),
                'coreValues' => $coreValues->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'title' => $value->title,
                        'type' => $value->type,
                        'description' => $value->type === 'caption' ? nl2br($value->description) : null,
                        'image' => $value->type === 'image' ? asset('storage/' . $value->image) : null
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
            ],
            'himpunan' => $himpunan ? [
                'name' => $himpunan->name,
                'description' => nl2br($himpunan->description),
                'logo' => $himpunan->logo ? asset('storage/' . $himpunan->logo) : null
            ] : null
        ]);
    }

    public function department()
    {
        $departments = Department::with([
            'members' => function ($query) {
                $query->orderBy('position');
            }
        ])
            ->orderByRaw("CASE WHEN dept_name = 'TRIMITRA' THEN 0 ELSE 1 END")
            ->get()
            ->map(function ($department) {
                return [
                    'id' => $department->id,
                    'dept_name' => $department->dept_name,
                    'image' => $department->image ? "/storage/{$department->image}" : null,
                    // Board of Department Members
                    'board_members' => $department->members
                        ->where('job_type', 'board_of_dept')
                        ->values()
                        ->map(fn($member) => $this->mapMember($member))
                        ->toArray(),
                    // Vice Board of Department Members
                    'vice_board_members' => $department->members
                        ->where('job_type', 'vice_board_of_dept')
                        ->values()
                        ->map(fn($member) => $this->mapMember($member))
                        ->toArray(),
                    // Section Head Members
                    'section_heads' => $department->members
                        ->where('job_type', 'section_head_dept')
                        ->values()
                        ->map(fn($member) => $this->mapMember($member))
                        ->toArray(),
                    // Staff Members
                    'staff_members' => $department->members
                        ->where('job_type', 'staff')
                        ->values()
                        ->map(fn($member) => $this->mapMember($member))
                        ->toArray()
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
                'slug' => $article->slug,
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
            'popularPosts' => $this->getPopularPosts(),
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
                'slug' => $article->slug,
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
            'popularPosts' => $this->getPopularPosts(),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'has_more_pages' => $articles->hasMorePages()
            ]
        ]);
    }

    public function articleDetail($slug)
    {
        $article = Article::with(['category', 'user', 'tags'])
            ->where('status', 'publish')
            ->where('slug', $slug)
            ->firstOrFail();

        // Update view counter
        DB::table('articles')
            ->where('slug', $slug)
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
                    'slug' => $relatedArticle->slug,
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
                'slug' => $article->slug,
                'content' => $article->content,
                'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                'created_at' => $article->created_at->format('d M Y'),
                'author' => $article->user->name,
                'viewed' => $viewCount,
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
            'popularPosts' => $this->getPopularPosts(),
            'relatedArticles' => $relatedArticles
        ]);
    }

    // Add new method for popular posts
    private function getPopularPosts()
    {
        return Article::with(['category', 'user'])
            ->where('status', 'publish')
            ->orderBy('viewed', 'desc')
            ->take(7)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => Str::limit(strip_tags($article->content), 150),
                    'featured_image' => $article->featured_image ? "/storage/{$article->featured_image}" : null,
                    'created_at' => $article->created_at->format('d M Y'),
                    'viewed' => $article->viewed,
                    'category' => [
                        'id' => $article->category->id,
                        'name' => $article->category->name
                    ]
                ];
            });
    }

    public function events()
    {
        $departments = Department::withCount(['events'])->get();

        $events = Event::with(['department', 'dates'])
            ->latest()
            ->paginate(12);

        $mappedEvents = $events->getCollection()->map(function ($event) {
            $today = now();
            $eventDates = $event->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();

            // Default status adalah past
            $status = 'past';

            if ($firstDate && $lastDate) {
                // Jika tanggal hari ini sama dengan salah satu tanggal event
                foreach ($eventDates as $date) {
                    // Ubah format tanggal event menjadi Y-m-d saja
                    $eventDate = Carbon::parse($date->event_date)->toDateString();
                    if ($eventDate === $today->toDateString()) {
                        $status = 'ongoing';
                        break;
                    }
                }

                // Jika bukan ongoing, cek apakah upcoming
                if ($status !== 'ongoing') {
                    $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                    if ($firstEventDate > $today->toDateString()) {
                        $status = 'upcoming';
                    }
                }
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 200),
                'fee_type' => $event->fee_type,
                'fee_amount' => $event->fee_amount,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'status' => $status,
                'department' => [
                    'id' => $event->department->id,
                    'name' => $event->department->dept_name
                ]
            ];
        });

        return Inertia::render('Events', [
            'departments' => $departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->dept_name,
                    'events_count' => $department->events_count
                ];
            }),
            'events' => $mappedEvents,
            'popularEvents' => $this->getPopularEvents(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'has_more_pages' => $events->hasMorePages()
            ]
        ]);
    }

    public function eventsByDepartment($departmentId)
    {
        $departments = Department::withCount(['events'])->get();

        $events = Event::with(['department', 'dates'])
            ->where('department_id', $departmentId)
            ->latest()
            ->paginate(12);

        $mappedEvents = $events->getCollection()->map(function ($event) {
            $today = now();
            $eventDates = $event->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();

            // Default status adalah past
            $status = 'past';

            if ($firstDate && $lastDate) {
                // Jika tanggal hari ini sama dengan salah satu tanggal event
                foreach ($eventDates as $date) {
                    // Ubah format tanggal event menjadi Y-m-d saja
                    $eventDate = Carbon::parse($date->event_date)->toDateString();
                    if ($eventDate === $today->toDateString()) {
                        $status = 'ongoing';
                        break;
                    }
                }

                // Jika bukan ongoing, cek apakah upcoming
                if ($status !== 'ongoing') {
                    $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                    if ($firstEventDate > $today->toDateString()) {
                        $status = 'upcoming';
                    }
                }
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 200),
                'fee_type' => $event->fee_type,
                'fee_amount' => $event->fee_amount,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'status' => $status,
                'department' => [
                    'id' => $event->department->id,
                    'name' => $event->department->dept_name
                ]
            ];
        });

        return Inertia::render('Events', [
            'departments' => $departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->dept_name,
                    'events_count' => $department->events_count
                ];
            }),
            'events' => $mappedEvents,
            'activeDepartment' => $departmentId,
            'popularEvents' => $this->getPopularEvents(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'has_more_pages' => $events->hasMorePages()
            ]
        ]);
    }

    public function eventDetail($id)
    {
        $event = Event::with(['department', 'dates'])
            ->where('id', $id)
            ->firstOrFail();

        $eventDates = $event->dates->sortBy('event_date');
        $firstDate = $eventDates->first();
        $lastDate = $eventDates->last();

        // Default status adalah past
        $status = 'past';

        if ($firstDate && $lastDate) {
            $today = now();

            // Jika tanggal hari ini sama dengan salah satu tanggal event
            foreach ($eventDates as $date) {
                // Ubah format tanggal event menjadi Y-m-d saja
                $eventDate = Carbon::parse($date->event_date)->toDateString();
                if ($eventDate === $today->toDateString()) {
                    $status = 'ongoing';
                    break;
                }
            }

            // Jika bukan ongoing, cek apakah upcoming
            if ($status !== 'ongoing') {
                $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                if ($firstEventDate > $today->toDateString()) {
                    $status = 'upcoming';
                }
            }
        }

        // Get related events from same department tanpa validasi status
        $relatedEvents = Event::with(['department', 'dates'])
            ->where('department_id', $event->department_id)
            ->where('id', '!=', $event->id)
            ->latest()
            ->get()
            ->map(function ($relatedEvent) {
                $today = now();
                $eventDates = $relatedEvent->dates->sortBy('event_date');
                $firstDate = $eventDates->first();
                $lastDate = $eventDates->last();

                // Default status adalah past
                $status = 'past';

                if ($firstDate && $lastDate) {
                    // Jika tanggal hari ini sama dengan salah satu tanggal event
                    foreach ($eventDates as $date) {
                        // Ubah format tanggal event menjadi Y-m-d saja
                        $eventDate = Carbon::parse($date->event_date)->toDateString();
                        if ($eventDate === $today->toDateString()) {
                            $status = 'ongoing';
                            break;
                        }
                    }

                    // Jika bukan ongoing, cek apakah upcoming
                    if ($status !== 'ongoing') {
                        $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                        if ($firstEventDate > $today->toDateString()) {
                            $status = 'upcoming';
                        }
                    }
                }

                return [
                    'id' => $relatedEvent->id,
                    'event_name' => $relatedEvent->event_name,
                    'event_detail' => Str::limit(strip_tags($relatedEvent->event_detail), 150),
                    'event_flyer' => $relatedEvent->event_flyer ? "/storage/{$relatedEvent->event_flyer}" : null,
                    'start_date' => $firstDate ? $firstDate->event_date : null,
                    'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                    'fee_type' => $relatedEvent->fee_type,
                    'status' => $status,
                    'department' => [
                        'id' => $relatedEvent->department->id,
                        'name' => $relatedEvent->department->dept_name
                    ]
                ];
            });

        return Inertia::render('EventDetail', [
            'event' => [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => $event->event_detail,
                'fee_type' => $event->fee_type,
                'fee_amount' => $event->fee_amount,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'event_gallery' => $event->event_gallery ? array_map(function ($image) {
                    return "/storage/{$image}";
                }, $event->event_gallery) : [],
                'event_doc' => $event->event_doc ? "/storage/{$event->event_doc}" : null,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                'status' => $status,
                'dates' => $event->dates->map(function ($date) {
                    return [
                        'event_date' => $date->event_date,
                        'event_time' => $date->event_time ? Carbon::parse($date->event_time)->format('H:i:s') : null
                    ];
                }),
                'department' => [
                    'id' => $event->department->id,
                    'name' => $event->department->dept_name
                ]
            ],
            'popularEvents' => $relatedEvents,
            'relatedEvents' => $relatedEvents
        ]);
    }

    // Add new method for popular events
    private function getPopularEvents()
    {
        return Event::with(['department', 'dates'])
            ->latest()
            ->take(7)
            ->get()
            ->map(function ($event) {
                $eventDates = $event->dates->sortBy('event_date');
                $firstDate = $eventDates->first();
                $lastDate = $eventDates->last();

                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_detail' => Str::limit(strip_tags($event->event_detail), 150),
                    'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                    'start_date' => $firstDate ? $firstDate->event_date : null,
                    'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null,
                    'fee_type' => $event->fee_type,
                    'department' => [
                        'id' => $event->department->id,
                        'name' => $event->department->dept_name
                    ]
                ];
            });
    }

    public function eventCalendar()
    {
        $events = Event::with(['department', 'dates'])
            ->latest()
            ->get()
            ->map(function ($event) {
                $today = now();
                $eventDates = $event->dates->sortBy('event_date');
                $firstDate = $eventDates->first();
                $lastDate = $eventDates->last();

                // Default status adalah past
                $status = 'past';

                if ($firstDate && $lastDate) {
                    // Jika tanggal hari ini sama dengan salah satu tanggal event
                    foreach ($eventDates as $date) {
                        // Ubah format tanggal event menjadi Y-m-d saja
                        $eventDate = Carbon::parse($date->event_date)->toDateString();
                        if ($eventDate === $today->toDateString()) {
                            $status = 'ongoing';
                            break;
                        }
                    }

                    // Jika bukan ongoing, cek apakah upcoming
                    if ($status !== 'ongoing') {
                        $firstEventDate = Carbon::parse($firstDate->event_date)->toDateString();
                        if ($firstEventDate > $today->toDateString()) {
                            $status = 'upcoming';
                        }
                    }
                }

                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_detail' => Str::limit(strip_tags($event->event_detail), 200),
                    'fee_type' => $event->fee_type,
                    'fee_amount' => $event->fee_amount,
                    'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                    'dates' => $event->dates->map(function ($date) {
                        return [
                            'event_date' => $date->event_date,
                            'event_time' => $date->event_time
                        ];
                    }),
                    'status' => $status,
                    'department' => [
                        'id' => $event->department->id,
                        'name' => $event->department->dept_name
                    ]
                ];
            });

        return Inertia::render('EventCalendar', [
            'events' => $events
        ]);
    }
}