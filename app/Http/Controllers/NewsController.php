<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\News;
use App\Models\Category;

class NewsController extends Controller

{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed'], 400);
        }

        if (Auth::guard('sanctum')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('sanctum')->user();
            $token = $user->createToken('token-name')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }



     // List all news (GET)
     public function index()
     {
         $news = News::all();
         return response()->json($news);
     }
     public function getLatestNews()
     {
         $latestNews = News::where('end_date', '>=', now()) // Exclude expired news
             ->orderBy('start_date', 'desc') // Order by publication date in descending order
             ->get();
 
         return response()->json($latestNews);
     }
 

   // Create a new news (POST)
   public function store(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'title' => 'required',
           'content' => 'required',
           'category' => 'required',
           'date_debut' => 'required',
           'date_expiration' => 'required',
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => 'Validation failed'], 400);
       }

       $news = News::create($request->all());

       return response()->json($news, 201);
   }

   // Update a news by ID (PUT)
   public function update(Request $request, $id)
   {
       $news = News::find($id);

       if (!$news) {
           return response()->json(['error' => 'News not found'], 404);
       }

       $news->update($request->all());

       return response()->json($news, 200);
   }

   // Delete a news by ID (DELETE)
   public function destroy($id)
   {
       $news = News::find($id);

       if (!$news) {
           return response()->json(['error' => 'News not found'], 404);
       }

       $news->delete();

       return response()->json(['message' => 'News deleted'], 204);
   }

   // Get a specific news by ID (GET)
   public function show($id)
   {
       $news = News::find($id);

       if (!$news) {
           return response()->json(['error' => 'News not found'], 404);
       }

       return response()->json($news, 200);
   }




   public function getArticlesInCategoryAndSubcategories($categoryId)
{
    // Initialisez un tableau pour stocker les articles
    $articles = [];

    // Recherchez la catégorie actuelle
    $category = Category::find($categoryId);

    if ($category) {
        // Récupérez les articles associés à cette catégorie
        $articles = $category->articles;

        // Parcourez les sous-catégories récursivement
        foreach ($category->subcategories as $subcategory) {
            $subArticles = $this->getArticlesInCategoryAndSubcategories($subcategory->id);
            $articles = $articles->merge($subArticles);
        }
    }

    return $articles;
}


    public function Search($categoryName)
{
    // Search for the category by its name
    $category = Category::where('name', $categoryName)->first();

    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    // Retrieve articles associated with this category and exclude expired news
    $articles = $category->articles()
        ->where('date_expiration', '>=', now())
        ->get();

    return response()->json($articles);
}

}
