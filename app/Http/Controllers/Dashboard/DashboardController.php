<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Url;
use App\Models\User;
use App\Models\Visit;
use App\Services\KeyGeneratorService;
use App\Services\LinkService;
use App\Services\UserService;
use App\Services\VisitService;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin', except: ['view', 'overviewPerUser'])];
    }

    /**
     * Show the dashboard and the URL list.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view()
    {
        return view('backend.dashboard', [
            'url' => app(Url::class),
        ]);
    }

    /**
     * Show all short URLs created by all users.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function allUrlView()
    {
        return view('backend.url-list');
    }

    /**
     * Display the stat view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function overview()
    {
        return view('backend.overview', [
            'url' => app(Url::class),
            'user' => app(User::class),
            'visit' => app(Visit::class),
            'userService' => app(UserService::class),
            'linkService' => app(LinkService::class),
            'visitService' => app(VisitService::class),
            'keyGenService' => app(KeyGeneratorService::class),
        ]);
    }

    /**
     * Display the stat view for specific user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function overviewPerUser(User $user)
    {
        Gate::authorize('authorOrAdmin', $user);

        return view('backend.overview_peruser', [
            'user' => $user,
            'url' => app(Url::class),
            'linkService' => app(LinkService::class),
            'visitService' => app(VisitService::class),
        ]);
    }

    /**
     * Show all short links from specific user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function userLinkView(string $author)
    {
        if ($author == 'guests') {
            $author = 'Guests';
            $authorId = Url::GUEST_ID;
        } else {
            $authorId = User::where('name', $author)->first()->id;
        }

        return view('backend.url-list-by-user', [
            'authorName' => $author,
            'authorId' => $authorId,
        ]);
    }

    /**
     * Show about page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function aboutView()
    {
        return view('backend.about', [
            'keyGenService' => app(KeyGeneratorService::class),
        ]);
    }
}
