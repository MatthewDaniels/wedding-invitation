<?php
namespace app\routes;
use RedBean_Facade as R;

$userNames = 'It\'s time for a Marry Party!';

$app->get('/', function() use ($app, $userNames) {
    $app->view()->appendData(array('names'=>$userNames));
	$app->render('index.twig');
})->name('Home');

$app->get('/:id', function ($id) use ($app, $userNames) {
    //Show user identified by $id
    //

    // get the user record from the DB, based on the $id passed in
    //
    // grab the userna`mes and parse them
    // $userNames = $id;

    // $peep = R::load( 'peeps', 5 ); //reloads our book

    $peep = R::findOne( 'peeps', ' hashed_id = ? ', [$id] );

    $peep && $userNames = $peep->names;

    $app->view()->appendData(array('names'=>$userNames));
    $app->render('index.twig');
});


/**
 * Create a new entry
 */
$app->post('/:peep', function() use ($app) {

    $body = json_decode($app->request->getBody());

    // update DB here
    $peep_db = R::dispense( 'peeps' );

    $peep_db->email = $body->email;
    $peep_db->names = $body->names;
    $peep_db->hashed_id = md5( strval($body->email) . strval($body->names) );

    $id = R::store( $peep_db );

    if($id != 0) {
        // store a hashed version of the id too

        // $peep_db->hashed_id = md5(strval($id));

        // success
        $success = array('success' => 1, 'message' => 'Great work - new peep added with the following details ' . var_dump($peep_db) );
    } else {
        // fail
        $success = array('success' => 0, 'message' => 'Uh oh, something went wrong ' . var_dump($peep_db) );
    }

    // set the return headers
    $app->response->headers->set('Content-Type', 'application/json');


    // return the success param as JSON
    echo json_encode($success);


    // exit time
    // exit();
});
