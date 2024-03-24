<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idea;
use App\Models\GiftList;
use App\Models\IdeaReserved;
use App\Models\IdeaPurchased;
use App\Models\FollowedList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class GiftListController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): Response
    {
        $authUserId = Auth::id();

        // Get list id from url
        $list = GiftList::find($id);

        // Get ALL ideas (except archived) from specific list (id in url)
        $ideas = Idea::where('list_id', $id)
            ->whereNot('status', 'archived')
            ->orderBy('brand')
            ->orderByDesc('favorite')
            ->orderBy('price')
            ->orderBy('idea')
            ->get();

        // Get AVAILABLE ideas from specific list (id in url)
        $ideas_available = Idea::where('list_id', $id)->where('status', 'available')
            ->orderBy('brand')
            ->orderByDesc('favorite')
            ->orderBy('price')
            ->orderBy('idea')
            ->get();

        // Get RESERVED ideas from specific list (id in url)
        $ideas_reserved = IdeaReserved::with('idea', 'user')
        ->where('gift_list_id', $id)
        ->orderByDesc('updated_at')
        ->get();

        // Get PURCHASED and not archived ideas from specific list (id in url)
        $ideas_purchased = IdeaPurchased::with('idea', 'user')
        ->where('gift_list_id', $id)
        ->where('archived', 0)
        ->orderByDesc('updated_at')
        ->get();

        // Get lists followed by auth user
        $followedLists = FollowedList::where('user_id', $authUserId)->get();

        return Inertia::render('GiftList/Show', [
            'list' => $list,
            'ideas' => $ideas,
            'ideas_available' => $ideas_available,
            'ideas_reserved' => $ideas_reserved,
            'ideas_purchased' => $ideas_purchased,
            'followedLists' => $followedLists,
        ]);
    }

    /**
     * Display a listing of the gift lists.
     */
    public function index(): Response
    {
        $authUser = Auth::user();
        $authUserId = Auth::id();

        // Get all users except auth user
        $users = User::where('id', '!=', $authUserId)->get();

        $dateFormat = 'd/m/Y';

        // Get lists CREATED by auth user
        $mylists = GiftList::where('user_id', $authUserId)->orderBy('created_at', 'desc')->get();
        // Date formatting
        foreach ($mylists as $mylist) {
            $mylist->formatted_created_at = Carbon::parse($mylist->created_at)->format($dateFormat);
            $mylist->lastUpdatedAt = Idea::where('list_id', $mylist->id)->max('created_at');
            $mylist->formatted_updated_at = Carbon::parse($mylist->lastUpdatedAt)->format($dateFormat);
            $mylist->isEmpty = Idea::where('list_id', $mylist->id)->count() === 0;
        }

        // Get lists FOLLOWED by auth user
        $followedLists = $authUser->followedLists()->get();
        // Date formatting
        foreach ($followedLists as $followedList) {
            $followedList->formatted_created_at = Carbon::parse($followedList->created_at)->format($dateFormat);
            $followedList->lastUpdatedAt = Idea::where('list_id', $followedList->id)->max('created_at');
            $followedList->formatted_updated_at = Carbon::parse($followedList->lastUpdatedAt)->format($dateFormat);
            $followedList->isEmpty = Idea::where('list_id', $followedList->id)->count() === 0;
        }

        return Inertia::render('GiftList/Index', [
            'users' => $users,
            'mylists' => $mylists,
            'followedLists' => $followedLists,
        ]);
    }

    /**
     * Display a listing of the gift lists TO FOLLOW.
     */
    public function listsToFollow(): Response
    {
        // Get all users except the auth user
        $authUserId = Auth::id();
        $users = User::where('id', '!=', $authUserId)->get();

        // Get lists to follow
        $authUser = Auth::user();
        $followedListIds = $authUser->followedLists()->pluck('gift_lists.id')->all();
        $listsToFollow = GiftList::whereNot('user_id', $authUserId)
        ->where('isPrivate', 0)
        ->whereNotIn('id', $followedListIds)
        ->orderBy('created_at', 'desc')
        ->get();

        // Date formatting
        $dateFormat = 'd/m/Y';
        foreach ($listsToFollow as $listToFollow) {
            $listToFollow->formatted_created_at = Carbon::parse($listToFollow->created_at)->format($dateFormat);
            $listToFollow->lastUpdatedAt = Idea::where('list_id', $listToFollow->id)->max('created_at');
            $listToFollow->formatted_updated_at = Carbon::parse($listToFollow->lastUpdatedAt)->format($dateFormat);
            $listToFollow->isEmpty = Idea::where('list_id', $listToFollow->id)->count() === 0;
        }

        return Inertia::render('Users/ListsToFollow', [
            'users' => $users,
            'listsToFollow' => $listsToFollow,
        ]);
    }

    /**
     * Display a listing of the FOLLOWED gift lists.
     */
    public function followedLists(): Response
    {
        // Get lists followed by auth user
        $authUser = Auth::user();
        $followedLists = $authUser->followedLists()->get();

        // Date formatting
        $dateFormat = 'd/m/Y';
        foreach ($followedLists as $followedList) {
            $followedList->formatted_created_at = Carbon::parse($followedList->created_at)->format($dateFormat);
            $followedList->lastUpdatedAt = Idea::where('list_id', $followedList->id)->max('created_at');
            $followedList->formatted_updated_at = Carbon::parse($followedList->lastUpdatedAt)->format($dateFormat);
            $followedList->isEmpty = Idea::where('list_id', $followedList->id)->count() === 0;
        }

        return Inertia::render('Users/FollowedLists', [
            'followedLists' => $followedLists,
        ]);
    }

    /**
     * Display a listing of the auth user's gift lists.
     */
    public function authLists(): Response
    {
        $authUserId = Auth::id();

        // Get lists of auth user
        $publicLists = GiftList::with('user:id,name')->where('user_id', $authUserId)->where('isPrivate', 0)->latest()->get();
        $privateLists = GiftList::with('user:id,name')->where('user_id', $authUserId)->where('isPrivate', 1)->latest()->get();

        return Inertia::render('GiftList/AuthLists', [
            'publicLists' => $publicLists,
            'privateLists' => $privateLists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('GiftList/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $string = 'required|string|max:255';

        $rules = [
            'user_name' => $string,
            'name' => $string,
            'isPrivate' => 'required|boolean',
        ];

        // If list is private, private_code is not required
        if ($request->input('isPrivate')) {
            $rules['private_code'] = 'nullable|string|max:255';
        } else {
            $rules['private_code'] = $string;
        }

        $validated = $request->validate($rules);

        $request->user()->gift_lists()->create($validated);

        return redirect(route('lists.authLists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GiftList $list): RedirectResponse
    {
        // Only the auth user can update the list
        $this->authorize('update', $list);

        $string = 'nullable|string|max:255';

        $validated = $request->validate([
            'user_name' => $string,
            'name' => $string,
            'isPrivate' => 'boolean',
            'private_code' => $string,
        ]);

        $list->update($validated);

        return redirect(route('lists.authLists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function archive(GiftList $list): RedirectResponse
    {
        IdeaPurchased::where('gift_list_id', $list->id)->update(['archived' => true]);
        Idea::where('list_id', $list->id)->where('status', 'purchased')->update(['status' => 'archived']);

        return redirect(route('lists.show', $list->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GiftList $list): RedirectResponse
    {
        //Only the auth user can delete the list
        $this->authorize('delete', $list);

        $list->delete();

        return redirect(route('lists.authLists'));
    }

    /**
     * Follow a list
     */
    public function followList(Request $request): RedirectResponse
    {
        $listId = $request->input('gift_list_id');
        $privateCode = $request->input('private_code');
        $correctPrivateCode = GiftList::where('id', $listId)->value('private_code');
        // dd($request->all());

        // $encodingPrivateCode = mb_detect_encoding($privateCode);
        // $encodingCorrectPrivateCode = mb_detect_encoding($correctPrivateCode);

        // Comparaison insensible à la casse (strcasecmp) et aux espaces (trim)
        if (strcasecmp(trim($privateCode), trim($correctPrivateCode)) === 0) {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'gift_list_id' => 'required|integer',
                'private_code' => 'required|string',
            ]);
            $request->user()->followed_lists()->create($validated);
            return redirect(route('lists.followedLists'));
        } else {
            return redirect()->back()->withErrors(['private_code' => 'Ce code est incorrect pour la liste demandée.']);
        }
    }

}
