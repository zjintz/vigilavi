<?php

namespace App\Util;

/**
 * A class to get some facts about the words of a wordset.
 *
 */
class WordCounter 
{
    public function getWordsStats($words): array
    {
        $totalWords = count($words);

        if ($totalWords == 0){
            return [];
        }
        $nullWords = 0;
        $addedWords = 0;
        $commentedWords = 0;
        foreach ($words as $word) {
            $text = $word->getText();
            if ($this->isStringVoid($text)) {
                $nullWords+=1;
                continue;
            }
            if (!(preg_match('/\s/', $text))) {
                if ($text[0] !== "#") {
                    $addedWords +=1;
                    continue;
                }
                $commentedWords +=1;
                continue;
            }
            $nullWords+=1;
        }
        $stats = [];
        $stats['total_words'] = $totalWords;
        $stats['added_words'] = $addedWords;
        $stats['commented_words'] = $commentedWords;
        $stats['null_words'] = $nullWords;
        return $stats;
    }

    /**
     * Checks if a string is empty or has only white spaces.
     *
     */
    private function isStringVoid($text)
    {
        if (ctype_space($text) || ($text === '')) {
            return true;
        }
        return false;
    }
}
