<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    private function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $count = 1;

        $query = Article::where('slug', $slug);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = Str::slug($title) . '-' . $count;
            $query = Article::where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            $count++;
        }

        return $slug;
    }

    public function index()
    {
        return Inertia::render('Articles/Index', [
            'articles' => Article::with(['category', 'tags'])->latest()->get(),
            'categories' => Category::all(),
            'tags' => Tag::all()
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Category::create($request->all());

        return redirect()->back();
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category->update($request->all());

        return redirect()->back();
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'array',
            'status' => 'required|in:draft,publish,highlight'
        ]);

        $data = $request->except('featured_image', 'tags');
        $data['slug'] = $this->generateUniqueSlug($request->title);

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'publish';
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $data['featured_image'] = $path;
        }

        $article = $request->user()->articles()->create($data);

        // Handle tags
        if ($request->has('tags')) {
            $tags = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
            });

            $article->tags()->sync($tags->pluck('id'));
        }

        return redirect()->back();
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'array',
            'status' => 'required|in:draft,publish,highlight'
        ]);

        $data = $request->except('featured_image', 'tags');

        // Only update slug if title changed
        if ($article->title !== $request->title) {
            $data['slug'] = $this->generateUniqueSlug($request->title, $article->id);
        }

        if ($request->hasFile('featured_image')) {
            Storage::disk('public')->delete($article->featured_image);
            $path = $request->file('featured_image')->store('articles', 'public');
            $data['featured_image'] = $path;
        }

        $article->update($data);

        // Handle tags
        if ($request->has('tags')) {
            $tags = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
            });

            $article->tags()->sync($tags->pluck('id'));
        }

        return redirect()->back();
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->back();
    }

    // Update route binding to use slug
    public function show(Article $article)
    {
        return Inertia::render('Articles/Show', [
            'article' => $article->load(['category', 'tags', 'user'])
        ]);
    }
}