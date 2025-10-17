<?php

return [
    // Adjust these defaults as needed (CAD)
    'default_base_by_status' => [
        'Full-time'   => 38000, // ~CA$18/hr × 40 hrs/week
        'Part-time'   => 19000, // ~half of full-time workload
        'Contractual' => 32000, // often slightly lower than full-time (no benefits)
        'Intern'      => 12000, // assuming part-time paid internship (~CA$20/hr × 15 hrs/week)
    ],
];
