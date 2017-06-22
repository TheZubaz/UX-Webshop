<?php
    require_once "models/game.php";

    class gamesController {

        public static function overview() {
            if (isset($_POST['var2']) && !empty($_POST['var2'])) {
                Base::Redirect($GLOBALS['config']['base_url'].'games/overview/1/'.Base::Sanitize($_POST['var2']));
            } elseif (isset($_POST['var2']) && empty($_POST['var2'])) {
                Base::Redirect($GLOBALS['config']['base_url'].'games/overview/1');
            }

            if (isset($var[3])) {
                $page = (int) Base::Sanitize( $var[3] );
                if ($page < 1) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $url = str_replace('0', '1', $url);

                    Base::Redirect( $url );
                }
            } else {
                $page = 1;
            }

            if (isset($var[4])) {
                $search = base::Sanitize($var[4]);
                $games = Game::searchByName($search, 12, (($page - 1) * 12) );
            } else {
                $games = Game::searchByName('', 12, (($page - 1) * 12) );
            }

            if (isset($var[4])) {
                $searchpar = '/'.$var[4];
            } else {
                $searchpar = null;
            }

            Base::Render('games/overview', [
                'games' => $games,
                'page' => $page,
                'searchpar' => $searchpar
            ]);
        }

        public static function view($var) {
            $id = Base::Sanitize( $var[2] );
            $game = Game::Find($id);
            
            if ( $game ) {
                $views = Game::addView($id);

                Base::Render('games/view', [
                    'game' => $game,
                    'views' => $views
                ]);
            } else {
                Base::Render('pages/error');
            }
        }

        public static function create() {
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 777) {
                if (
                    isset($_POST['game']) &&
                    isset($_POST['game']['name']) && !empty($_POST['game']['name']) &&
                    isset($_POST['game']['price']) && !empty($_POST['game']['price']) &&
                    isset($_POST['game']['descr']) && !empty($_POST['game']['descr']) &&
                    (isset($_FILES['cover']) && $_FILES['cover']['size'] > 0)
                ) {
                    $game = new Game(
                        Base::Genetate_id(),
                        Base::Sanitize( $_POST['game']['name'] ),
                        (int) Base::Sanitize( $_POST['game']['price'] ),
                        Base::Sanitize( $_POST['game']['descr'] ),
                        Base::Upload_file( $_FILES['cover'] )
                    );

                    if ($game->save()) {
                        Base::Redirect($GLOBALS['config']['base_url'].'games/overview');
                    } else {
                        Base::Render('pages/error');
                    }
                } else {
                    Base::Render('games/create');
                }
            } else {
                Base::Render('pages/error');
            }
        }

        public static function edit($var) {
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 777) {
                $id = Base::Sanitize( $var[2] );
                $game = Game::find($id);

                if (
                    isset($_POST['game']) &&
                    isset($_POST['game']['name']) &&
                    isset($_POST['game']['price']) &&
                    isset($_POST['game']['descr'])
                ) {
                    $game->name = Base::Sanitize( $_POST['game']['name'] );
                    $game->price = (int) Base::Sanitize( $_POST['game']['price'] );
                    $game->descr = Base::Sanitize( $_POST['game']['descr'] );
                    
                    if (isset($_FILES['cover']) && $_FILES['cover']['size'] > 0) {
                        $game->cover = Base::Upload_file( $_FILES['cover'] );
                    }
                    
                    if ($game->save()) {
                        Base::Redirect($GLOBALS['config']['base_url'] . "games/view/" . $game->id);
                    } else {
                        Base::Render('pages/error');
                    }
                } else {
                    Base::Render('games/edit', [
                        'game' => $game
                    ]);
                }
            } else {
                Base::Render('pages/error');
            }
        }

        public static function delete($var) {
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 777) {
                $id = Base::Sanitize( $var[2] );
                $game = Game::find($id);

                if ( $game ) { 
                    $game->delete();
                    Base::Redirect($GLOBALS['config']['base_url'] . 'games/overview');
                } else {
                    Base::Render('pages/error');
                }
            } else {
                Base::Render('pages/error');
            }
        }
    }
?>
