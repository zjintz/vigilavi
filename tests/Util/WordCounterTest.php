<?php
namespace App\Tests\Util;

use App\Entity\Word;
use App\Util\WordCounter;
use PHPUnit\Framework\TestCase;

class WordCounterTest extends TestCase
{
    /**
     * Test the getWordsStats from the WordCounter class.
     *
     */
    public function testGetWordsStatsVoid()
    {
        $wCounter = new WordCounter();
        $response = $wCounter->getWordsStats([]);
        $this->assertEquals(
            [],
            $response,
            "If there are no words the return must be [].");
    }

    /**
     * Test the getWordsStats from the WordCounter class.
     *
     */
    public function testGetWordsStats()
    {
        $wCounter = new WordCounter();
        $word = new Word();
        $word->setText('start');
        $comment = new Word();
        $comment->setText('#---');
        $invalid = new Word();
        $invalid->setText('start end something');
        $response = $wCounter->getWordsStats([$word, $comment, $invalid]);
        $this->assertEquals(
            [
                'total_words' => 3,
                'added_words' => 1,
                'commented_words' =>1,
                'null_words' => 1
                
            ],
            $response,
            "If there are no words the return must be []."
        );
    }
}
