<?php

namespace aliirfaan\LaravelSimpleApi;

use Symfony\Component\HttpFoundation\Request;

/**
 * HypermediaRelation
 * 
 * A helper class to generate HATEAOS hypermedia links
 */
class HypermediaRelation
{
    /**
     * Conveys an identifier for the link's context. Usually a link pointing to the resource itself.
     */
    public const REL_SELF = 'self';

    /**
     * Refers to a link that can be used to create a new resource.
     */
    public const REL_CREATE = 'create';

    /**
     * Refers to editing (or partially updating) the representation identified by the link. Use this to represent a PATCH operation link.
     */
    public const REL_EDIT = 'edit';


    /**
     * Refers to deleting a resource identified by the link. Use this Extended link relation type to represent a DELETE operation link.
     */
    public const REL_DELETE = 'delete';

    /**
     * Refers to completely update (or replace) the representation identified by the link. Use this Extended link relation type to represent a PUT operation link.
     */
    public const REL_REPLACE = 'replace';

    /**
     * Refers to the first page of the result list.
     */
    public const REL_FIRST = 'first';

    /**
     * Refers to the last page of the result list provided total_required is specified as a query parameter.
     */
    public const REL_LAST = 'last';

    /**
     * Refers to the next page of the result list.
     */
    public const REL_NEXT = 'next';

    /**
     * Refers to the previous page of the result list.
     */
    public const REL_PREVIOUS = 'prev';

    /**
     * Refers to a collections resource (e.g /v1/users).
     */
    public const REL_COLLECTION = 'collection';

    /**
     * Points to a resource containing the latest (e.g., current) version.
     */
    public const REL_LATEST_VERSION = 'latest-version';

    /**
     * Refers to a resource that can be used to search through the link's context and related resources.
     */
    public const REL_SEARCH = 'search';

    /**
     * Refers to a parent resource in a hierarchy of resources.
     */
    public const REL_UP = 'up';

    public function generateHypermediaLink($href, $method = Request::METHOD_POST, $rel = self::REL_SELF, $title = null)
    {
        return [
            'title' => $title,
            'href' => $href,
            'method' => $method,
            'rel' => $rel
        ];
    }
}