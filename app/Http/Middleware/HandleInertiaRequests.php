<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\Resolvers\PlatformService;
use App\Services\Remote\ServerConnectivityService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'corporate' => fn () => $request->user()?->corporateInfo()->getResults()?->getAttributes(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'newContact' => fn () => $request->session()->get('newContact'),
                'contactUpdated' => fn () => $request->session()->get('contactUpdated'),
                'newTemplate' => fn () => $request->session()->get('newTemplate'),
                'templateUpdated' => fn () => $request->session()->get('templateUpdated'),
                'blast' => fn () => $request->session()->get('blast'),
            ],
            'platform' => fn () => PlatformService::detect(),
            'server' => [
                'connectivity' => ServerConnectivityService::isOnline(),
            ]
        ];
    }
}
