<?php
// Block direct PHP execution in uploads folder
http_response_code(403);
exit('Access denied.');
