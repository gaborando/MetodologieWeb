<?php
    var_dump(password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]));