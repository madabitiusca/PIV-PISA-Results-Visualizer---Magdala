<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'pass123';