
Markdown for OfficialWorkflow

![OfficialWorkflow.svg](OfficialWorkflow.svg)



---
## Transition: fetch_wiki

### fetch_wiki.Guard

        onGuard()
        // scrape wiki data
```php
#[AsGuardListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
public function onGuard(GuardEvent $event): void
{
    // @todo: move to guard: in workflow
    if (!$this->getOfficial($event)->getWikidataId()) {
        $event->setBlocked(true, "missing wiki id.");
    }
}
```
[View source](dt-demo/blob/main/src/Workflow/OfficialWorkflow.php#L49-L55)

### fetch_wiki.Transition

        onFetchWiki()
        // scrape wiki data
```php
#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
public function onFetchWiki(TransitionEvent $event): void
{
    $official = $this->getOfficial($event);
    $this->wikiService->setCacheTimeout(60 * 60 * 24);
    $wikiData = $this->wikiService->fetchWikidataPage($official->getWikidataId());
    $official->setWikiData($wikiData->toArray());
}
```
[View source](dt-demo/blob/main/src/Workflow/OfficialWorkflow.php#L58-L64)




---
## Transition: resize

### resize.Transition

        onResize()
        // dispatch resize to sais
```php
    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_RESIZE)]
    public function onResize(TransitionEvent $event): void
    {
        $official = $this->getOfficial($event);
        $wikiData = $official->getWikiData();

//        $p18 = $wikiData['properties']['P18'];
        /** @var Collection $values */

        $values = $wikiData['properties']['P18']['values']??[];
//        dump($p18, $values->getIterator());
        /** @var Value $item */
        $images = [];
        foreach ($values as $item) {
            // we could do this in an async message, too.
            if ($url = $item['id']) {
                $official->setOriginalImageUrl($url);
                $response = $this->saisClientService->dispatchProcess(new ProcessPayload(LoadCongressCommand::SAIS_CLIENT, [
                    $url
                ],
                thumbCallbackUrl: $x=$this->urlGenerator->generate('app_webhook', ['id' => $official->getId()], $this->urlGenerator::ABS_URL)
                ));
                break; // first one only, for now.
            }
        }
    }
```
[View source](dt-demo/blob/main/src/Workflow/OfficialWorkflow.php#L67-L91)


