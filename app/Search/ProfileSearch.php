<?php

declare(strict_types=1);

namespace App\Search;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileSearch
{
    /**
     * @var int LIMIT
     */
    private const PAGE_LIMIT = 36;

    private const SELECT_COLUMNS = [
        'id',
        'first_name',
        'last_name',
        'slug',
        'gender',
        'institution',
        'instruments',
        'birth_date',
        'bio',
        'profile_image_url',
    ];


    /**
     * Query class which returns profiles depending on chosen filters
     *
     * @param Request $filters
     */
    public function apply(Request $filters): LengthAwarePaginator
    {
        $selectColumns = $this->getSelectColumns('profiles', self::SELECT_COLUMNS);

        $selectColumnsString = implode(',', $selectColumns);

        /** @var LengthAwarePaginator $profiles */
        $profiles = Profile::with([
            'location',
        ])
            ->when($filters->input('search_term'), function (Builder $query, string $term) use ($selectColumnsString) {
                $query->selectRaw($selectColumnsString . ', MATCH(bio) AGAINST(?) AS score', [$term]);
            }, function (Builder $query) use ($selectColumns) {
                $query->select($selectColumns);
            })
            ->when($filters->input('gender'), function (Builder $query, string $gender) {
                $query->where('gender', '=', $gender);
            })
            ->when($filters->input('country'), function (Builder $query, string $country) use ($filters) {
                $query->join('locations', 'profiles.location_id', '=', 'locations.id')
                    ->where('locations.country', $country);

                $query->when($filters->input('city'), function (Builder $query, string $city) {
                    $query->where('locations.city', $city);
                });
            })
            ->when($filters->input('instruments'), function (Builder $query, string $instrument) {
                $query->whereJsonContains('instruments', $instrument);
            })
            ->when($filters->input('search_term'), function (Builder $query, string $term) {
                $query->where(function (Builder $query) use ($term) {
                    $upperTermsArray = explode(' ', strtoupper($term));
                    $bindings = self::getDynamicsBindings($upperTermsArray);
                    $query->whereRaw("MATCH(bio) AGAINST(?)", $term)
                        ->orWhereRaw('UPPER(institution) IN (' . $bindings . ')', $upperTermsArray)
                        ->orWhereRaw('UPPER(first_name) IN (' . $bindings . ')', $upperTermsArray)
                        ->orWhereRaw('UPPER(last_name) IN (' . $bindings . ')', $upperTermsArray);
                });
            })
            ->limit($this->getPageLimit((int)$filters->input('limit'), self::PAGE_LIMIT))
            ->when($filters->input('search_term'), function (Builder $query) {
                $query->orderByRaw('score DESC');
            }, function (Builder $query) use ($filters) {
                if ($filters->input('order_by') === 'created_at') {
                    $query->orderBy('profiles.created_at', 'desc');
                } else {
                    $query->orderBy('profiles.updated_at', 'desc');
                }
            })
            ->groupBy('profiles.id')
            ->paginate($this->getPageLimit((int)$filters->input('limit'), self::PAGE_LIMIT))
            ->appends($filters->all())
            ->withPath(route('search'));

        return $profiles;
    }

    /**
     * Returns array of table & column strings for SELECT query
     * @param string $table
     * @param string[] $columns
     * @return string[]
     */
    protected function getSelectColumns(string $table, array $columns): array
    {
        return array_map(function ($column) use ($table) {
            return $table . '.' . $column;
        }, $columns);
    }

    /**
     * Returns minimum number of either the requested or declared limit
     */
    protected function getPageLimit(?int $requestedLimit, int $limit): int
    {
        return $requestedLimit ? min($requestedLimit, $limit) : $limit;
    }

    /**
     * Get string of concatenated question marks separated by a comma
     */
    protected function getDynamicsBindings(array $array): string
    {
        $bindingsArray = array_map(function ($single) {
            return '?';
        }, $array);

        return implode(',', $bindingsArray);
    }
}
