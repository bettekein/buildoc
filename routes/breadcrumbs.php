<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// Projects
Breadcrumbs::for('projects.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('案件一覧', route('projects.index'));
});

// Projects > Create
Breadcrumbs::for('projects.create', function (BreadcrumbTrail $trail) {
    $trail->parent('projects.index');
    $trail->push('新規案件作成', route('projects.create'));
});

// Projects > Edit
Breadcrumbs::for('projects.edit', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push('案件編集: ' . $project->name, route('projects.edit', $project));
});

// Project > Quotation
Breadcrumbs::for('quotations.edit', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push($project->name . ' (見積)', route('quotations.edit', $project));
});

// Project > Billings
Breadcrumbs::for('billings.index', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push($project->name . ' (請求)', route('billings.index', $project));
});

// Project > Billings > Edit
Breadcrumbs::for('billings.edit', function (BreadcrumbTrail $trail, $project, $billing) {
    $trail->parent('billings.index', $project);
    $trail->push('請求書編集 (第' . $billing->billing_round . '回)', route('billings.edit', [$project, $billing]));
});

// Audits
Breadcrumbs::for('audits.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('監査ログ', route('audits.index'));
});
