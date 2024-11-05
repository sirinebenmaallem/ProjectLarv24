<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('scategories')->get()->toArray();
        $res = array_reverse($articles);
        return response()->json($res);
    }
    public function store(Request $request)
    {
        $article = new Article();
        $article->designation= $request->input('designation');
        $article->marque= $request->input('marque');
        $article->reference= $request->input('reference');
        $article->qtestock= $request->input('qtestock');
        $article->prix= $request->input('prix');
        $article->imageart= $request->input('imageart');
        $article->scategorieID= $request->input('scategorieID');
        $article->save();
        return response()->json($article);
    }
    public function show($id)
    {
        $article= Article::find($id);
        return response()->json($article);
    }
    public function update($id, Request $request)
    {
        $article = Article::find($id);
        $article->update($request->all());
        return response()->json($article);
    }
    public function destroy($id)
    {
        $article = Article::find($id);
        $article->delete();
        return response()->json(['message' => 'Article deletedsuccessfully']);
    }

    public function showArticlesPagination(Request $request)
    {
        // Récupérer les paramètres de requête
        $filtre = $request->input('filtre', '');
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        // Requête Eloquent avec filtre sur la désignation et pagination
        $query = Article::where('designation', 'like', '%' . $filtre . '%')
            ->with('scategories') // une relation définie avec scategories
            ->orderBy('id', 'desc'); // Tri descendant par ID
        // Pagination
        $totalArticles = $query->count();
        $articles = $query->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();
        // Calculer le nombre total de pages
        $totalPages = ceil($totalArticles / $pageSize);
        // Retourner la réponse en JSON
        return response()->json([
            'products' => $articles,
            'totalPages' => $totalPages,
        ]);
    }

    public function paginationPaginate()
    {
        $perPage = request()->input('pageSize', 2); // Récupère la valeurdynamique pour la pagination
        // Récupère le filtre par désignation depuis la requête
        $filterDesignation = request()->input('filtre');
        // Construction de la requête
        $query = Article::with('scategories');
        // Applique le filtre sur la désignation s'il est fourni
        if ($filterDesignation) {
            $query->where('designation', 'like', '%' . $filterDesignation .'%');
        }
        // Paginer les résultats après avoir appliqué le filtre
        $articles = $query->paginate($perPage);
        // Retourne le résultat en format JSON API
        return response()->json([
            'products' => $articles->items(), // Les articles paginés
            'totalPages' => $articles->lastPage(), // Le nombre de pages
        ]);
    }
}
