<?php

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 */

namespace Horde\Support\Test\Numerizer\Locale;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Horde_Support_Numerizer;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
#[CoversClass(Horde_Support_Numerizer_Locale_De::class)]
 */
class DeTest extends TestCase
{
    public function testStraightParsing()
    {
        $numerizer = Horde_Support_Numerizer::factory(['locale' => 'de']);
        $strings = [
            [1, 'eins'],
            [5, 'fünf'],
            [10, 'zehn'],
            [11, 'elf'],
            [12, 'zwölf'],
            [13, 'dreizehn'],
            [14, 'vierzehn'],
            [15, 'fünfzehn'],
            [16, 'sechzehn'],
            [17, 'siebzehn'],
            [18, 'achtzehn'],
            [19, 'neunzehn'],
            [20, 'zwanzig'],
            [27, 'siebenundzwanzig'],
            [31, 'einunddreißig'],
            [59, 'neunundfünfzig'],
            [100, 'einhundert'],
            [100, 'ein hundert'],
            [150, 'hundertundfünfzig'],
            [150, 'einhundertundfünfzig'],
            [200, 'zweihundert'],
            [500, 'fünfhundert'],
            [999, 'neunhundertneunundneunzig'],
            [1000, 'eintausend'],
            [1200, 'zwölfhundert'],
            [1200, 'eintausendzweihundert'],
            [17000, 'siebzehntausend'],
            [21473, 'einundzwanzigtausendvierhundertdreiundsiebzig'],
            [74002, 'vierundsiebzigtausendzwei'],
            [74002, 'vierundsiebzigtausendundzwei'],
            [99999, 'neunundneunzigtausendneunhundertneunundneunzig'],
            [100000, 'hunderttausend'],
            [100000, 'einhunderttausend'],
            [250000, 'zweihundertfünfzigtausend'],
            [1000000, 'eine million'],
            [1250007, 'eine million zweihundertfünfzigtausendundsieben'],
            [1000000000, 'eine milliarde'],
            [1000000001, 'eine milliarde und eins'],
        ];

        foreach ($strings as $pair) {
            $this->assertEquals((string) $pair[0], $numerizer->numerize($pair[1]));
        }
    }

    public function testLocaleVariants()
    {
        $this->assertInstanceOf('Horde_Support_Numerizer_Locale_De', Horde_Support_Numerizer::factory(['locale' => 'de_DE']));
        $this->assertInstanceOf('Horde_Support_Numerizer_Locale_De', Horde_Support_Numerizer::factory(['locale' => 'de_at']));
    }

    public function testStaticNumerize()
    {
        $this->assertEquals(1250007, Horde_Support_Numerizer::numerize('eine million zweihundertfünfzigtausendundsieben', ['locale' => 'de']));
    }
}
