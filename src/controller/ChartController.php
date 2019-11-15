<?php

final class ChartController extends PhabricatorController {    
  public function handleRequest(AphrontRequest $request) {    
    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb(pht('Chart'));
    return $this->newPage()
    ->setTitle(pht('Chart'))
    ->setCrumbs($crumbs);
  }

  
}