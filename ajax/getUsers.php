<?php
    require_once("../models/classes/userDataset.php");

    echo json_encode(UserDataSet::getUsersByName($_GET["search"]));