<?php


//--------------Singleton pattern used in one of the previous projects.------------------->
class Database
{
    private $connection;

    public static $instance = null;

    public function __construct()
    {
        if (self::$instance === null) {
            self::$instance = $this;
        }
        $this->connection = new Medoo([
            'database_type' => 'mysql',
            'database_name' => '**DATABASE NAME HERE**',
            'server' => 'localhost',
            'username' => '**DATABASE USER NAME HERE**',
            'password' => '**DATABASE PASSWORD HERE**'
        ]);
    }

    public static function getInstance(): self
    {
        return self::$instance ?? new Database();
    }

    public function connection()
    {
        return $this->connection;
    }
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------



//----------Controller i made for user matching in previous project. ----------
class ShowMatchesController extends Controller
{
    public function __invoke()
    {
        $userId = auth()->id();

        $allMyLikes = DB::table('decisions')
            ->where('user_id', '=', $userId)
            ->where('decision_type', '=', 'like')
            ->get();

        $allWhoLikedMe = DB::table('decisions')
            ->where('decision_to', '=', $userId)
            ->where('decision_type', '=', 'like')
            ->get();

        $matches = $this->getMatches($allMyLikes, $allWhoLikedMe);

        $matchProfiles = $this->getMatchProfiles($matches);


        return view('matches', [
            'matches' => $matchProfiles
        ]);
    }

    private function getMatches($allMyLikes, $allWhoLikedMe)
    {
        $matches = [];
        foreach ($allMyLikes as $myLike) {
            foreach ($allWhoLikedMe as $likedMe) {
                if ($myLike->decision_to == $likedMe->user_id) {
                    $matches[] = $myLike;
                }
            }
        }
        return $matches;
    }

    private function getMatchProfiles($matches)
    {
        $profiles = [];

        $allProfiles = DB::table('profiles')->get();

        foreach ($allProfiles as $profile) {
            foreach ($matches as $match) {
                if (((int)$match->decision_to) === $profile->user_id) {
                    $profiles[] = $profile;
                }
            }
        }

        return $profiles;
    }
}