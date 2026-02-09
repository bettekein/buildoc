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

// Projects > Allocations
Breadcrumbs::for('projects.allocations', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push($project->name . ' (手配管理)', route('projects.allocations', $project));
});

// Project > Quotation
Breadcrumbs::for('quotations.edit', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push($project->name . ' (見積)', route('quotations.edit', $project));
});

// Project > Billings > Create
Breadcrumbs::for('billings.create', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects.index');
    $trail->push($project->name, route('projects.edit', $project));
    $trail->push('請求書作成');
});

// Project > Billings > Edit
Breadcrumbs::for('billings.edit', function (BreadcrumbTrail $trail, $billing) {
    $trail->parent('projects.index');
    $trail->push($billing->project->name, route('projects.edit', $billing->project));
    $trail->push('請求書編集', route('billings.edit', $billing));
});

// Masters
Breadcrumbs::for('masters.subcontractors', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('下請業者マスタ', route('masters.subcontractors'));
});

Breadcrumbs::for('masters.staff', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('スタッフマスタ', route('masters.staff'));
});

Breadcrumbs::for('masters.vehicles', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('車両マスタ', route('masters.vehicles'));
});

Breadcrumbs::for('masters.tools', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('工具マスタ', route('masters.tools'));
});

// Audits
Breadcrumbs::for('audits.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('監査ログ', route('audits.index'));
});
