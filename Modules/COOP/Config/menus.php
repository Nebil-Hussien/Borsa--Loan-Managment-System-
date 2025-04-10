<?php

return [
    ['name' => 'COOP', 'is_parent' => 1, 'module' => 'COOP', 'slug' => 'coops', 'parent_slug' => '', 'url' => 'coops', 'icon' => 'fas fa-building', 'order' => 6, 'permissions' => ''],
    ['name' => 'View COOP', 'is_parent' => 0, 'module' => 'COOP', 'slug' => 'view_coops', 'parent_slug' => 'coops', 'url' => 'coops', 'icon' => 'far fa-circle', 'order' => 7, 'permissions' => 'coop.coops.index'],
    ['name' => 'Create COOP', 'is_parent' => 0, 'module' => 'COOP', 'slug' => 'create_coops', 'parent_slug' => 'coops', 'url' => 'coops/create', 'icon' => 'far fa-circle', 'order' => 8, 'permissions' => 'coop.coops.create'],
];