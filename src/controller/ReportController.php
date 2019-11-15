<?php

final class ReportController extends PhabricatorController {
  public function handleRequest(AphrontRequest $request) {
    $viewer = $request->getViewer();

    $menu = id(new PHUIObjectItemListView())
      ->setUser($viewer)
      ->setBig(true);

    $menu->addItem(
      id(new PHUIObjectItemView())
        ->setHeader(pht('Employee Workload'))
        ->setImageIcon('fa-user-circle-o')
        ->setHref($this->getApplicationURI('employeeworkload/'))
        ->setClickable(true)
        ->addAttribute(
          pht('View workload of employees and manage it.')));

    $menu->addItem(
      id(new PHUIObjectItemView())
        ->setHeader(pht('Task Tree'))
        ->setImageIcon('fa-sitemap')
        ->setHref($this->getApplicationURI('tasktree/'))
        ->setClickable(true)
        ->addAttribute(
          pht('View tree of tasks with estimated and actual implementation time.')));

    // $menu->addItem(
    //   id(new PHUIObjectItemView())
    //     ->setHeader(pht('Projects implementation time chart'))
    //     ->setImageIcon('fa-area-chart')
    //     ->setHref($this->getApplicationURI('chart/'))
    //     ->setClickable(true)
    //     ->addAttribute(pht('View chart displaying time spended on individual projects.')));    

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb(pht('Home'));
    $crumbs->setBorder(true);

    $title = pht('Reporting module');

    $box = id(new PHUIObjectBoxView())
      ->setHeaderText($title)
      ->setBackground(PHUIObjectBoxView::WHITE_CONFIG)
      ->setObjectList($menu);

    $view = id(new PHUITwoColumnView())
      ->setFixed(true)
      ->setFooter($box);

    return $this->newPage()
      ->setTitle($title)
      ->setCrumbs($crumbs)
      ->appendChild($view);
  }

}
