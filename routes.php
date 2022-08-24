<?php

require_once("router.php");

// ##################################################
// ##################################################
// ##################################################

get('/', 'index.php');

get('/gallery', 'API/galleryGet.php');

post('/gallery', 'API/galleryPost.php');

get('/gallery/$path', 'API/galleryPathGet.php');

post('/gallery/$path', 'API/galleryPathPost.php');

get('/images/$dimensions/$path', 'API/imageGet.php');

delete('/gallery/$path', 'API/galleryPathDelete.php');


