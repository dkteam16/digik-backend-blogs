<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class NewsletterSubscriberController extends Controller
{
    public function index(): View
    {
        $totalCount = NewsletterSubscriber::count();

        return view('admin.newsletter.index', compact('totalCount'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = NewsletterSubscriber::query();

        if ($request->filled('q')) {
            $query->where('email', 'like', '%' . $request->q . '%');
        }

        return DataTables::of($query)
            ->addColumn('checkbox', fn (NewsletterSubscriber $subscriber) => '<input type="checkbox" name="subscriber_ids[]" value="'.$subscriber->id.'" form="bulk-form" class="form-check-input cbox">')
            ->addColumn('date_col', fn (NewsletterSubscriber $subscriber) => $subscriber->created_at->format('M d, Y h:i A'))
            ->addColumn('actions', fn (NewsletterSubscriber $subscriber) => '
                <form action="'.route('admin.newsletter.destroy', $subscriber).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this subscriber?\')">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
            ')
            ->rawColumns(['checkbox', 'actions'])
            ->make(true);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber deleted successfully!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action'         => 'required|in:delete',
            'subscriber_ids' => 'required|array',
        ]);

        NewsletterSubscriber::whereIn('id', $request->subscriber_ids)->delete();

        return back()->with('success', 'Bulk action applied successfully!');
    }
}
