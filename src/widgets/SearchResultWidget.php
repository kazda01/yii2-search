<?php

namespace kazda01\search\widgets;

use kazda01\search\services\SearchService;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class SearchResultWidget extends Widget
{
    /**
     * @var string $searchId Search ID, must be same as in module config
     */
    public $searchId;

    /**
     * @var string $search Search string
     */
    public $search;

    /**
     * @var bool $showMatchAttribute Show match attribute
     */
    public bool $showMatchAttribute = true;

    /**
     * @var bool $ignoreLimits Ignore query pagination limits
     */
    public bool $ignoreLimits = false;

    /**
     * @var string $searchResultClass Class for search result
     */
    public $searchResultClass = 'rounded';

    public function init(): void
    {
        Yii::setAlias('@kazda01Search', __DIR__);

        if ($this->searchId == null) {
            throw new InvalidConfigException("Parameter 'searchId' is required.");
        }

        parent::init();
    }

    public function getViewPath()
    {
        $viewPath = Yii::getAlias('@kazda01Search/views');
        return $viewPath !== false ? $viewPath : parent::getViewPath();
    }

    private function removeAccents(string $text): string
    {
        $accents = ['à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y', 'ā' => 'a', 'ă' => 'a', 'ą' => 'a', 'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'č' => 'c', 'ď' => 'd', 'đ' => 'd', 'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h', 'ħ' => 'h', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'į' => 'i', 'i̇' => 'i', 'ı' => 'i', 'ĵ' => 'j', 'ķ' => 'k', 'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l', 'ń' => 'n', 'ņ' => 'n', 'ň' => 'n', 'ŉ' => 'n', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o', 'ŕ' => 'r', 'ŗ' => 'r', 'ř' => 'r', 'ś' => 's', 'ŝ' => 's', 'ş' => 's', 'š' => 's', 'ţ' => 't', 'ť' => 't', 'ŧ' => 't', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ŷ' => 'y', 'ź' => 'z', 'ż' => 'z', 'ž' => 'z', 'ƀ' => 'b', 'ɓ' => 'b', 'ƃ' => 'b', 'ƈ' => 'c', 'ɗ' => 'd', 'ƌ' => 'd', 'ƒ' => 'f', 'ɠ' => 'g', 'ɨ' => 'i', 'ƙ' => 'k', 'ƚ' => 'l', 'ɲ' => 'n', 'ƞ' => 'n', 'ɵ' => 'o', 'ơ' => 'o', 'ƥ' => 'p', 'ƫ' => 't', 'ƭ' => 't', 'ʈ' => 't', 'ư' => 'u', 'ʋ' => 'v', 'ƴ' => 'y', 'ƶ' => 'z', 'ǆ' => 'd', 'ǉ' => 'l', 'ǌ' => 'n', 'ǎ' => 'a', 'ǐ' => 'i', 'ǒ' => 'o', 'ǔ' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'ǟ' => 'a', 'ǡ' => 'a', 'ǥ' => 'g', 'ǧ' => 'g', 'ǩ' => 'k', 'ǫ' => 'o', 'ǭ' => 'o', 'ǰ' => 'j', 'ǳ' => 'd', 'ǵ' => 'g', 'ǹ' => 'n', 'ǻ' => 'a', 'ǿ' => 'o', 'ȁ' => 'a', 'ȃ' => 'a', 'ȅ' => 'e', 'ȇ' => 'e', 'ȉ' => 'i', 'ȋ' => 'i', 'ȍ' => 'o', 'ȏ' => 'o', 'ȑ' => 'r', 'ȓ' => 'r', 'ȕ' => 'u', 'ȗ' => 'u', 'ș' => 's', 'ț' => 't', 'ȟ' => 'h', 'ȡ' => 'd', 'ȥ' => 'z', 'ȧ' => 'a', 'ȩ' => 'e', 'ȫ' => 'o', 'ȭ' => 'o', 'ȯ' => 'o', 'ȱ' => 'o', 'ȳ' => 'y', 'ȴ' => 'l', 'ȵ' => 'n', 'ȶ' => 't', 'ȷ' => 'j', 'ⱥ' => 'a', 'ȼ' => 'c', 'ⱦ' => 't', 'ȿ' => 's', 'ɀ' => 'z', 'ʉ' => 'u', 'ɇ' => 'e', 'ɉ' => 'j', 'ɋ' => 'q', 'ɍ' => 'r', 'ɏ' => 'y', 'ɕ' => 'c', 'ɖ' => 'd', 'ɟ' => 'j', 'ɦ' => 'h', 'ɫ' => 'l', 'ɬ' => 'l', 'ɭ' => 'l', 'ɱ' => 'm', 'ɳ' => 'n', 'ɼ' => 'r', 'ɽ' => 'r', 'ɾ' => 'r', 'ʂ' => 's', 'ʄ' => 'j', 'ʐ' => 'z', 'ʑ' => 'z', 'ʝ' => 'j', 'ʠ' => 'q', 'ͣ' => 'a', 'ͤ' => 'e', 'ͥ' => 'i', 'ͦ' => 'o', 'ͧ' => 'u', 'ͨ' => 'c', 'ͩ' => 'd', 'ͪ' => 'h', 'ͫ' => 'm', 'ͬ' => 'r', 'ͭ' => 't', 'ͮ' => 'v', 'ͯ' => 'x', 'ᵢ' => 'i', 'ᵣ' => 'r', 'ᵤ' => 'u', 'ᵥ' => 'v', 'ᵬ' => 'b', 'ᵭ' => 'd', 'ᵮ' => 'f', 'ᵯ' => 'm', 'ᵰ' => 'n', 'ᵱ' => 'p', 'ᵲ' => 'r', 'ᵳ' => 'r', 'ᵴ' => 's', 'ᵵ' => 't', 'ᵶ' => 'z', 'ᵻ' => 'i', 'ᵽ' => 'p', 'ᵾ' => 'u', 'ᶀ' => 'b', 'ᶁ' => 'd', 'ᶂ' => 'f', 'ᶃ' => 'g', 'ᶄ' => 'k', 'ᶅ' => 'l', 'ᶆ' => 'm', 'ᶇ' => 'n', 'ᶈ' => 'p', 'ᶉ' => 'r', 'ᶊ' => 's', 'ᶌ' => 'v', 'ᶍ' => 'x', 'ᶎ' => 'z', 'ᶏ' => 'a', 'ᶑ' => 'd', 'ᶒ' => 'e', 'ᶖ' => 'i', 'ᶙ' => 'u', '᷊' => 'r', 'ᷗ' => 'c', 'ᷚ' => 'g', 'ᷜ' => 'k', 'ᷝ' => 'l', 'ᷠ' => 'n', 'ᷣ' => 'r', 'ᷤ' => 's', 'ᷦ' => 'z', 'ḁ' => 'a', 'ḃ' => 'b', 'ḅ' => 'b', 'ḇ' => 'b', 'ḉ' => 'c', 'ḋ' => 'd', 'ḍ' => 'd', 'ḏ' => 'd', 'ḑ' => 'd', 'ḓ' => 'd', 'ḕ' => 'e', 'ḗ' => 'e', 'ḙ' => 'e', 'ḛ' => 'e', 'ḝ' => 'e', 'ḟ' => 'f', 'ḡ' => 'g', 'ḣ' => 'h', 'ḥ' => 'h', 'ḧ' => 'h', 'ḩ' => 'h', 'ḫ' => 'h', 'ḭ' => 'i', 'ḯ' => 'i', 'ḱ' => 'k', 'ḳ' => 'k', 'ḵ' => 'k', 'ḷ' => 'l', 'ḹ' => 'l', 'ḻ' => 'l', 'ḽ' => 'l', 'ḿ' => 'm', 'ṁ' => 'm', 'ṃ' => 'm', 'ṅ' => 'n', 'ṇ' => 'n', 'ṉ' => 'n', 'ṋ' => 'n', 'ṍ' => 'o', 'ṏ' => 'o', 'ṑ' => 'o', 'ṓ' => 'o', 'ṕ' => 'p', 'ṗ' => 'p', 'ṙ' => 'r', 'ṛ' => 'r', 'ṝ' => 'r', 'ṟ' => 'r', 'ṡ' => 's', 'ṣ' => 's', 'ṥ' => 's', 'ṧ' => 's', 'ṩ' => 's', 'ṫ' => 't', 'ṭ' => 't', 'ṯ' => 't', 'ṱ' => 't', 'ṳ' => 'u', 'ṵ' => 'u', 'ṷ' => 'u', 'ṹ' => 'u', 'ṻ' => 'u', 'ṽ' => 'v', 'ṿ' => 'v', 'ẁ' => 'w', 'ẃ' => 'w', 'ẅ' => 'w', 'ẇ' => 'w', 'ẉ' => 'w', 'ẋ' => 'x', 'ẍ' => 'x', 'ẏ' => 'y', 'ẑ' => 'z', 'ẓ' => 'z', 'ẕ' => 'z', 'ẖ' => 'h', 'ẗ' => 't', 'ẘ' => 'w', 'ẙ' => 'y', 'ẚ' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'ỉ' => 'i', 'ị' => 'i', 'ọ' => 'o', 'ỏ' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ụ' => 'u', 'ủ' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'ỳ' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỿ' => 'y', 'ⁱ' => 'i', 'ⁿ' => 'n', 'ₐ' => 'a', 'ₑ' => 'e', 'ₒ' => 'o', 'ₓ' => 'x', '⒜' => 'a', '⒝' => 'b', '⒞' => 'c', '⒟' => 'd', '⒠' => 'e', '⒡' => 'f', '⒢' => 'g', '⒣' => 'h', '⒤' => 'i', '⒥' => 'j', '⒦' => 'k', '⒧' => 'l', '⒨' => 'm', '⒩' => 'n', '⒪' => 'o', '⒫' => 'p', '⒬' => 'q', '⒭' => 'r', '⒮' => 's', '⒯' => 't', '⒰' => 'u', '⒱' => 'v', '⒲' => 'w', '⒳' => 'x', '⒴' => 'y', '⒵' => 'z', 'ⓐ' => 'a', 'ⓑ' => 'b', 'ⓒ' => 'c', 'ⓓ' => 'd', 'ⓔ' => 'e', 'ⓕ' => 'f', 'ⓖ' => 'g', 'ⓗ' => 'h', 'ⓘ' => 'i', 'ⓙ' => 'j', 'ⓚ' => 'k', 'ⓛ' => 'l', 'ⓜ' => 'm', 'ⓝ' => 'n', 'ⓞ' => 'o', 'ⓟ' => 'p', 'ⓠ' => 'q', 'ⓡ' => 'r', 'ⓢ' => 's', 'ⓣ' => 't', 'ⓤ' => 'u', 'ⓥ' => 'v', 'ⓦ' => 'w', 'ⓧ' => 'x', 'ⓨ' => 'y', 'ⓩ' => 'z', 'ⱡ' => 'l', 'ⱨ' => 'h', 'ⱪ' => 'k', 'ⱬ' => 'z', 'ⱱ' => 'v', 'ⱳ' => 'w', 'ⱴ' => 'v', 'ⱸ' => 'e', 'ⱺ' => 'o', 'ⱼ' => 'j', 'ꝁ' => 'k', 'ꝃ' => 'k', 'ꝅ' => 'k', 'ꝉ' => 'l', 'ꝋ' => 'o', 'ꝍ' => 'o', 'ꝑ' => 'p', 'ꝓ' => 'p', 'ꝕ' => 'p', 'ꝗ' => 'q', 'ꝙ' => 'q', 'ꝛ' => 'r', 'ꝟ' => 'v', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z'];
        return strtr($text, $accents);
    }

    private function normalizeText(string $text): string
    {
        return $this->removeAccents(mb_strtolower($text));
    }

    /**
     * Create match text with bolded all matches.
     *
     * @param string $text
     * @return string
     */
    public function createMatchText(string $text): string
    {
        $normalizedSearch = $this->normalizeText($this->search);
        $normalizedText = $this->normalizeText($text);

        $result = '';
        while (($position = mb_strpos($normalizedText, $normalizedSearch)) !== false) {
            $before = mb_substr($text, 0, $position);
            $match = mb_substr($text, $position, mb_strlen($this->search));

            $text = mb_substr($text, $position + mb_strlen($this->search));
            $normalizedText = $this->normalizeText($text);
            $result .= $before . '<b class="fw-bold">' . $match . '</b>';
        }
        $result .= $text;

        return $result;
    }

    public function run(): string
    {
        $searchService = new SearchService();
        $results = $searchService->search($this->search, $this->searchId, $this->ignoreLimits);

        return $this->render('searchWidget', [
            'results' => $results,
            'search' => $this->search,
            'widget' => $this,
        ]);
    }
}
