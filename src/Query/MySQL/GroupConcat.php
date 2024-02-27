<?php

namespace Smart\CoreBundle\Query\MySQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;

/**
 * Allows you to add the MySQL group_concat function to DQL
 * Based on https://github.com/beberlei/DoctrineExtensions/blob/master/src/Query/Mysql/GroupConcat.php
 */
class GroupConcat extends FunctionNode
{
    /** @var bool */
    public $isDistinct = false;

    /** @var array|null */
    public $pathExp = null;

    /** @var string|null */
    public $separator = null;

    /** @var OrderByClause|null */
    public $orderBy = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(Lexer::T_DISTINCT)) {
            $parser->match(Lexer::T_DISTINCT);

            $this->isDistinct = true;
        }

        // first Path Expression is mandatory
        $this->pathExp = [];
        if ($lexer->isNextToken(Lexer::T_IDENTIFIER)) {
            $this->pathExp[] = $parser->StringExpression();
        } else {
            $this->pathExp[] = $parser->SingleValuedPathExpression();
        }

        while ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->pathExp[] = $parser->StringPrimary();
        }

        if ($lexer->isNextToken(Lexer::T_ORDER)) {
            $this->orderBy = $parser->OrderByClause();
        }

        if ($lexer->isNextToken(Lexer::T_IDENTIFIER)) {
            if (strtolower($lexer->lookahead['value']) !== 'separator') { // @phpstan-ignore-line
                $parser->syntaxError('separator');
            }
            $parser->match(Lexer::T_IDENTIFIER);

            $this->separator = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        $result = 'GROUP_CONCAT(' . ($this->isDistinct ? 'DISTINCT ' : '');

        $fields = [];
        foreach ($this->pathExp as $pathExp) {
            $fields[] = $pathExp->dispatch($sqlWalker);
        }

        $result .= sprintf('%s', implode(', ', $fields));

        if ($this->orderBy) {
            $result .= ' ' . $sqlWalker->walkOrderByClause($this->orderBy);
        }

        if ($this->separator) {
            $result .= ' SEPARATOR ' . $sqlWalker->walkStringPrimary($this->separator);
        }

        $result .= ')';

        return $result;
    }
}
