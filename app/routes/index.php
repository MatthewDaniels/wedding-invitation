<?php
namespace app\routes;
use RedBean_Facade as R;

$userNames = 'It\'s time for a Marry Party!';


/**
 * Add peeps Route
 */
$app->get('/edit-peeps(/:peep)', function($peepId = null) use ($app) {
    $peep = R::findOne( 'peeps', ' hashed_id = ? ', [$peepId] );
    $peep = is_null($peep) ? R::findOne( 'peeps', ' id = ? ', [$peepId] ) : $peep;
    if(!is_null($peep)) {
        $app->view()->appendData(array('peep' => $peep->export() ));
    }
    $app->render('add.twig');
})->name('Add Peeps');



/**
 * Create a new entry
 */
$app->post('/update-peeps', function() use ($app) {

    $body = $app->request->post();

    // let's see if a user like this exists first

    $hashedID = isset($body['hashed_id']) ? $body['hashed_id'] : null;
    if(is_null($hashedID) || empty($hashedID)) {
        $hashedID = md5(strval($body['names']) . strval($body['emails']));
    }
    $existingPeep = R::findOne( 'peeps', ' hashed_id = ? ', [$hashedID] );

    // set the peep db variable
    $peep_db = is_null($existingPeep) ? R::dispense( 'peeps' ) : $existingPeep;

    // set the return headers
    $app->response->headers->set('Content-Type', 'application/json');

    $peep_db->emails = $body['emails'];
    $peep_db->names = $body['names'];
    $peep_db->hashed_id = $hashedID;

    $id = R::store( $peep_db );

    if($id != 0) {
        // store a hashed version of the id too

        // success
        $message = is_null($existingPeep) ? 'Great work - new peep added with the details on the data value' : 'Great work - updated peep with the details on the data value';
        $success = array('success' => 1, 'message' => $message, 'data' =>  $peep_db->export() );
    } else {
        // fail
        $success = array('success' => 0, 'message' => 'Uh oh, something went wrong ', 'data' => $peep_db->export() );
    }

    // return the success param as JSON
    echo json_encode($success);
});




// $app->get('/peeps', function () use ($app) {
//     $peeps = R::load( 'peeps' );

//     // set the return headers
//     $app->response->headers->set('Content-Type', 'application/json');

//     echo json_encode( $peeps );
// });

/**
 * ID based - grab a single
 */
$app->get('/:id', function ($id) use ($app, $userNames) {
    //Show user identified by $id
    //

    if($id === 'peeps') {
        $peeps = R::findAll( 'peeps' );

        // set the return headers
        $app->response->headers->set('Content-Type', 'application/json');
        echo json_encode( R::exportAll($peeps) );
    } else {
        $peep = R::findOne( 'peeps', ' hashed_id = ? ', [$id] );

        $peep && $userNames = $peep->names;

        $app->view()->appendData(array('names'=>$userNames));
        $app->render('index.twig');
    }

});


/**
 * DEfault Route
 */
$app->get('/', function() use ($app, $userNames) {
    $app->view()->appendData(array('names'=>$userNames));
    $app->render('index.twig');
})->name('Home');
