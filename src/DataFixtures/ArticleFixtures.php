<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Product;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private array $restaurantArticles = [
        'yume-sushi' => [
            [
                'title' => 'Jak zrobić sushi bez nori – 6 pomysłów na to czym zastąpić nori',
                'slug' => 'jak-zrobic-sushi-bez-nori-–-6-pomyslow-na-to-czym-zastapic-nori',
                'content' => <<<HTML
                    <div><strong>Nori</strong> to cienkie arkusze sprasowanych alg morskich, najczęściej używane do zawijania sushi w postaci rolek. Mają dość specyficzny, 'morski’ smak co nie każdemu może przypaść do gustu.&nbsp;<br><br></div><div>Najbardziej oczywistym rodzajem sushi bez nori są <strong>nigiri</strong>. Jednak co zrobić jeśli nie chcemy rezygnować z sushi w postaci rolek?&nbsp;<br><br></div><div>W tym artykule przygotowałem dla Ciebie 6 pomysłów na to czym można zastąpić nori.&nbsp;<br><br></div><div>Czym zastąpić nori – 6 propozycji<br><br></div><div>1. Ogórek&nbsp;</div><div>2. Papier ryżowy</div><div>3. Płatki sojowe</div><div>4. Omlet</div><div>5. Inari – smażone tofu</div><div>6. Biała rzodkiew<br><br></div>    
                HTML,
                'image' => 'jak-zrobic-sushi-bez-nori-–-6-pomyslow-na-to-czym-zastapic-nori.webp',
            ], [
                'title' => 'Sushi z ogórkiem – jak pokroić ogórka do sushi',
                'slug' => 'sushi-z-ogorkiem-–-jak-pokroic-ogorka-do-sushi',
                'content' => <<<HTML
                    <div><strong>Jaki ogórek do sushi</strong><br><br></div><div>Do sushi używa się przeważnie długich ogórków szklarniowych. Warto wybrać takie, które są możliwie jak <strong>najprostsze i o równej grubości.</strong> To bardzo ułatwi Ci ich krojenie. Ważne jest również, aby upewnić się, że wybierasz ogórki, które są <strong>świeże i jędrne</strong> – tak aby zachowały swoją <strong>chrupkość</strong>.<br><br></div><div>W Japonii używa się ogórków odmiany <strong>Southern Delight F1</strong>. Charakteryzją się jasnozielonym kolorem, cienką skórką i słodkim smakiem. Ich wysokie plony, odporność na choroby i wczesne dojrzewanie sprawiają, że są idealnym wyborem dla tych, którzy chcą cieszyć się świeżymi ogórkami z własnego ogrodu. Można je zebrać już po 45 dniach.<br><br>Odmiana Southern Delight F1 ma również świetną trwałość, dzięki czemu można przechowywać ogórki do dwóch tygodni w lodówce bez utraty jakości i smaku.<br><br></div><div><strong>Jak pokroić ogórka do sushi</strong><br><br></div><div>Sposób, który Ci pokażę świetnie sprawdzi się również do krojenia białej rzodkwi (daikon), zarówno w postaci świeżej jak i marynowanej (oshinko). Osobiście preferuję krojenie warzyw w cienkie paski – julienne, aczkowiek pokażę Ci również alternatywę.&nbsp;<br><br></div><div><strong>Sushi z ogórkiem – przepisy</strong><br><br></div><div>Ogórek jest jednym z najczęściej używanych <a href="https://jakzrobicsushi.pl/co-do-sushi-do-srodka-jakie-dodatki-do-sushi/">dodatków do sushi</a>. Jego delikatna, chrupiąca tekstura oraz świeży smak idealnie kontrastują z pozostałymi składnikami.&nbsp;<br><br></div><div><strong>Przepis na hosomaki z ogórkiem i sezamem czyli kappa maki</strong><br><br></div><div>Ogórek idealnie sprawdzi się w praktycznie każdym <a href="https://jakzrobicsushi.pl/rodzaje-sushi-poznaj-najpopularniejsze-odmiany-i-nazwy/">rodzaju sushi</a>. Nadaje chrupkości i świeżości.&nbsp;<br><br></div><div>Klasyczne i najprostsze połączenie to <strong>hosomaki z ogórkiem i sezamem</strong>. „Hoso” oznacza „cienki/chudy” (hosoi), tak więc będzie to cienka rolka z niewielką ilością ryżu, krojona na 6 lub 8 kawałków.<br><br></div><div>Jak ugotować ryż do sushi – w tym wpisie dowiesz się jak przygotować idealny ryż.<br><br></div><div>1. Bierzemy płat nori, przecinamy go na pół i kładziemy matową stroną do góry. Rozkładamy cienką warstwę ryżu zostawiając około 1cm marginesu od góry. Ważne aby go zbyt mocno nie ugniatać, rozłóż go możliwie delikatnie.<br><br></div><div>2. Posypujemy ryż sezamem. <a href="https://jakzrobicsushi.pl/jak-uprazyc-sezam-czyli-jak-przygotowac-sezam-do-sushi/">Sezam należy wcześniej uprażyć</a>. Możesz to zrobić podgrzewając go na suchej patelni lub wsadzić na 5min do piekarnika rozgrzanego do 180 stopni.&nbsp;<br><br></div><div>3. Następnie układamy cienkie paski ogórka.<br><br></div><div>4. Zawijamy przytrzymując całą rolkę palcami wskazującymi. Ogórka musi być na tyle dużo aby po zwinięciu nie powstał tzw. ślimak – czyli podwójna warstwa ryżu.<br><br></div><div>5. <strong>Nie moczymy końcówki nori wodą</strong>. Aby nori się do siebie dobrze skleiło, rozgniatamy w kilku miejscach ziarenko ryżu – tworząc w ten sposób „klej”.<br><br></div><div>6. Kroimy ostrym nożem na 6 lub 8 kawałków. &nbsp;<br><br></div><div><strong>Przepis na nigiri z ogórkiem</strong><br><br></div><div>Kolejną, najprostszą formą sushi z ogórkiem jest nigiri.&nbsp;<br><br></div><div>1. Ogórek kroimy w cienkie paski – im cieniej tym lepiej.&nbsp;<br><br></div><div>2. Z połówki nori odcinamy cienkie paski (~0,5cm)<br><br></div><div>3. Bierzemy około 22g ryżu, delikatnie formujemy w owalny kształt.<br><br></div><div>4. Nakładamy pocięty ogórek i delikatnie przyciskamy, formując kształt nigiri.&nbsp;<br><br></div><div>5. Całość owijamy paskiem nori. Na koniec posypujemy sezamem.&nbsp;<br><br></div><div>Przepis na gunkan z ogórkiem zamiast nori<br><br></div><div>Ogórek dobrze się sprawdzi jako <a href="https://jakzrobicsushi.pl/jak-zrobic-sushi-bez-nori-6-pomyslow-na-to-czym-zastapic-nori/">zamiennik nori</a>. Do tego celu będziemy potrzebować możliwie jak najcieńsze paski. Najłatwiej wykroić je przy pomocy obieraczki do warzyw.&nbsp;<br><br></div><div>Następnie, podobnie jak przy nigiri, bierzemy ryż i delikatnie formujemy owalny kształt.&nbsp;<br><br></div><div>Całość owijamy ogórkiem. Na ryż dodajemy odrobinę <a href="https://jakzrobicsushi.pl/kategoria-produktu/pasta-wasabi-do-sushi/">wasabi</a> oraz wybrany składnik. W moim przypadku jest to tatar z łososia.&nbsp;<br><br></div>
                HTML,
                'image' => 'sushi-z-ogorkiem-–-jak-pokroic-ogorka-do-sushi.webp'
            ],
        ],
    ];

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
        (new Filesystem)->mirror(
            $this->parameterBag->get('kernel.project_dir') . '/fixtures/uploads/articles/',
            $this->parameterBag->get('kernel.project_dir') . '/public/uploads/articles/',
        );

        foreach ($this->restaurantArticles as $restaurantSlug => $articlesData) {
            $restaurant = $manager->getRepository(Restaurant::class)->findOneBy(['slug' => $restaurantSlug]);

            foreach ($articlesData as $articleData) {
                $article = (new Article)
                    ->setRestaurant($restaurant)
                    ->setTitle($articleData['title'])
                    ->setSlug($articleData['slug'])
                    ->setContent($articleData['content'])
                    ->setImage($articleData['image']);

                $manager->persist($article);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
        ];
    }
}
