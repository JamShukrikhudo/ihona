<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'h2', 'h3', 'h4', 'ul', 'ol', 'li',
        'strong', 'em', 'b', 'i', 'blockquote', 'code', 'pre', 'a',
    ];

    public function sanitize(?string $html): string
    {
        if (! $html) {
            return '';
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="sanitized-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('sanitized-root');
        if (! $root) {
            return '';
        }

        $this->sanitizeChildren($root);

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return $result;
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                $replacement = $node->ownerDocument->createTextNode($node->textContent);
                $parent->replaceChild($replacement, $node);

                continue;
            }

            foreach (iterator_to_array($node->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                if ($tag !== 'a' || ! in_array($name, ['href', 'title'], true)) {
                    $node->removeAttribute($attribute->name);
                }
            }

            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if (! $this->isSafeLink($href)) {
                    $node->removeAttribute('href');
                } else {
                    $node->setAttribute('rel', 'noopener noreferrer nofollow');
                }
            }

            $this->sanitizeChildren($node);
        }
    }

    private function isSafeLink(string $href): bool
    {
        if ($href === '' || str_starts_with($href, '/') || str_starts_with($href, '#')) {
            return true;
        }

        return in_array(strtolower((string) parse_url($href, PHP_URL_SCHEME)), ['http', 'https', 'mailto'], true);
    }
}
