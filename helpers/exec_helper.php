<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function exec_bg($command)
{
	exec($command . ' >/dev/null 2>&1');
}