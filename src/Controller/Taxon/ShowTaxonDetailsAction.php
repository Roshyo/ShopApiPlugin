<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Taxon;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonDetailsViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowTaxonDetailsAction
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TaxonDetailsViewFactoryInterface */
    private $taxonViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonDetailsViewFactoryInterface $taxonViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
    }

    public function __invoke(Request $request): Response
    {
        $code = $request->attributes->get('code');
        $locale = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with code %s has not been found.', $code));
        }

        return $this->viewHandler->handle(View::create($this->taxonViewFactory->create($taxon, $locale), Response::HTTP_OK));
    }
}
