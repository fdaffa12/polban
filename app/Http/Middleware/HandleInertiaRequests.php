<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Setting;
use App\Models\AboutUs;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $settings = Setting::first();
        if ($settings) {
            $aboutUs = AboutUs::first();
            $settings->about_us_excerpt = $aboutUs ? Str::limit(strip_tags($aboutUs->au_desc), 500) : '';
            $navTitle = $aboutUs ? $aboutUs->au_title : '';
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'settings' => $settings,
            'navTitle' => $navTitle,
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
        ]);
    }
}
