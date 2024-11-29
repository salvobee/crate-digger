import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, usePage, router, useForm} from '@inertiajs/react';
import PriceDisplay from "@/Components/PriceDisplay.jsx";
import ConditionBadge from "@/Components/ConditionBadge.jsx";
import LabelBadge from "@/Components/LabelBadge.jsx";
import Pagination from '@/Components/Pagination';
import {FaRecordVinyl, FaRegSquare} from 'react-icons/fa';
import ReleaseStats from "@/Components/ReleaseStats.jsx";
import Facet from "@/Components/Facet.jsx";
import {useEffect, useState} from "react";
import {FaBoxArchive} from "react-icons/fa6";
import YearRangeSelector from "@/Components/YearRangeSelector.jsx";

export default function Show(props) {

    const { listings } = usePage().props
    const { criteria, crates, store, facets, parameters } = props;
    const {filters, sort } = parameters
    const {
        post,
        delete: destroy,
        processing,
        errors,
    } = useForm();

    const [activeFilters, setActiveFilters] = useState(filters)
    const [activeSort, setActiveSort] = useState(sort)


    // Sincronizza URL quando filtri o ordinamento cambiano
    useEffect(() => {
        console.log(crates)
        const queryParams = new URLSearchParams();

        queryParams.set("sort", activeSort);

        Object.entries(activeFilters).forEach(([key, values]) => {
            values.forEach((value) => queryParams.append(`filters[${key}][]`, value));
        });

        const newUrl = `?${queryParams.toString()}`;

        console.log(newUrl)
        // Controlla se l'URL è effettivamente diverso
        if (activeFilters !== filters || activeSort !== sort) {
            router.visit(newUrl, {
                onError: console.log
            })
        }
    }, [activeFilters, activeSort]);

    const handleAddFilter = (key, value) => {
        setActiveFilters(prev => ({
            ...prev,
            [key]: [...(prev[key] || []), value],
        }));
    };

    const handleRemoveFilter = (key, value) => {
        setActiveFilters(prev => {
            const newValues = (prev[key] || []).filter(v => v !== value);
            const { [key]: _, ...rest } = prev; // Rimuove il filtro se vuoto
            return newValues.length ? { ...rest, [key]: newValues } : rest;
        });
    };

    const handleSortChange = (event) => {
        console.log(event.target.value)
        setActiveSort(event.target.value);
    };

    const handleAddToCrate = (listingId) => {
        router.post(`/crates`, { listing: listingId }, { preserveScroll: true })
    };
    const handleRemoveFromCrate = (crateId) => {
        router.delete(`/crates/${crateId}`)
    };


    const handleRangeFilter = (key, value) => {
        setActiveFilters(prev => ({
            ...prev,
            [key]: [value],
        }));
    };
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {store.seller_username}'s Listings
                </h2>
            }
        >
            <Head title={`${store.seller_username} Listings`} />

            <span className="
            bg-red-50 bg-red-100 bg-red-200 bg-red-300 bg-red-400 bg-red-500 bg-red-600  bg-red-700 bg-red-900
            bg-green-50 bg-green-100 bg-green-200 bg-green-300 bg-green-400 bg-green-500  bg-green-600 bg-green-700 bg-green-900"/>
            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                        <div className="flex space-x-8">
                            <div className="w-1/5 flex flex-col space-y-4">
                                <h2 className="text-2xl">Filters</h2>

                                {Object.entries(activeFilters).length > 0  &&
                                    <div className="p-4 rounded-md bg-green-100">

                                        {Object.entries(activeFilters).map(([filterKey, filterValues]) => (
                                        <div key={filterKey}>
                                            <h3 className="font-bold capitalize">{filterKey.replace("_", " ")}</h3>
                                            <ul className="flex flex-row mt-2 space-x-2">
                                                {filterValues.map((value, index) => (
                                                    <button key={index}
                                                            onClick={() => handleRemoveFilter(filterKey, value)}
                                                        className="flex space-x-1 rounded-md shrink-0 px-2 py-1 text-xs bg-green-500 text-gray-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"
                                                             className="size-4">
                                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                                  d="M6 18 18 6M6 6l12 12"/>
                                                        </svg>

                                                        <span>{value}</span>
                                                    </button>
                                                ))}
                                            </ul>
                                        </div>))
                                        }
                                    </div>

                                }

                                <YearRangeSelector onChange={handleRangeFilter} activeFilters={activeFilters} />



                                {facets.map(facet => <Facet key={facet.title}
                                                            onChange={handleAddFilter}
                                                            facet={facet}
                                />)}
                            </div>

                            <div className="w-4/5">
                                <div className="mb-4 flex items-center justify-between">
                                <h1>
                                        <strong>{listings.total}</strong> listings in {store.seller_username}'s
                                        Inventory</h1>

                                    <div className="flex items-center space-x-2">
                                        <span>Sort by</span>
                                        <form>
                                            <select
                                                name="sort" onChange={handleSortChange}
                                                defaultValue={new URLSearchParams(window.location.search).get('sort') || 'default'}
                                            >
                                                {criteria.map(criteria => (
                                                    <option key={criteria.key}
                                                            value={criteria.key}>{criteria.description}
                                                    </option>
                                                ))}
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">Title</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">Want/Have</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">Price</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">Conditions</th>
                                    </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                    {listings.data.map(
                                        (listing) => {
                                            let crateLookup = crates.filter(item => item.listing_id === listing.id);
                                            const isInCrate = crateLookup.length > 0
                                            const crateId = isInCrate ? crateLookup[0].id : ''
                                            return (
                                            <tr key={listing.id}>
                                            <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">

                                                <div
                                                    className="max-w-md text-lg text-nowrap text-ellipsis  overflow-hidden flex space-x-2">
                                                    <button onClick={() => isInCrate ? handleRemoveFromCrate(crateId) : handleAddToCrate(listing.id)}
                                                            className={`group ${ isInCrate ? 'text-lime-600' : 'text-black'} font-bold flex items-center space-x-2`}
                                                            title={`${listing.release.artist} - ${listing.release.title}`}>
                                                        <span>
                                                            { isInCrate ?  <FaBoxArchive /> : <FaRecordVinyl /> }
                                                        </span>
                                                        <span className="flex items-center">
                                                            {listing.release.artist} - {listing.release.title}

                                                            <span className={`${
                                                                isInCrate ?
                                                                    'visible'
                                                                    : 'invisible group-hover:visible'
                                                            }
                                                                     ml-2`}>

                                                                {
                                                                    isInCrate ?
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                             viewBox="0 0 24 24" strokeWidth={1.5}
                                                                             stroke="currentColor" className="size-4 text-red-500">
                                                                            <path strokeLinecap="round"
                                                                                  strokeLinejoin="round"
                                                                                  d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                                                        </svg>

                                                                        : <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                             viewBox="0 0 24 24" strokeWidth={1.5}
                                                                             stroke="currentColor" className="size-6">
                                                                            <path strokeLinecap="round"
                                                                                  strokeLinejoin="round"
                                                                                  d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                                                        </svg>
                                                                }
                                                            </span>
                                                        </span>
                                                    </button>
                                                    {
                                                        isInCrate ? <a title="Listen to crate" href={`/crates?selected=${crateId}`}>
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" strokeWidth={1.5}
                                                                 stroke="currentColor" className="size-6">
                                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                                      d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z"/>
                                                            </svg>
                                                        </a> : ''
                                                    }
                                                </div>
                                                <div className="mt-4 flex items-center space-x-2">
                                                    <span className="font-bold">{listing.release.year}</span>
                                                    <span>/</span>
                                                    <LabelBadge label={listing.release.label}
                                                                catalogNumber={listing.release.catalog_number}/>
                                                </div>

                                                <div className="mt-4 flex flex-col space-y-2 max-w-md overflow-hidden">
                                                    <div className="flex space-x-1">
                                                        <span className="text-gray-600">Genre:</span>
                                                        {
                                                            listing.release.genres.length > 0 ?
                                                                (
                                                                    listing.release.genres.map(
                                                                        genre =>
                                                                            <span
                                                                                key={genre.id}
                                                                                className="flex items-center px-2 rounded-md text-xs bg-black text-white">
                                                                            {genre.name}
                                                                        </span>
                                                                    )
                                                                )
                                                                : <span className="text-gray-200">N/A</span>
                                                        }
                                                    </div>

                                                    <div className="flex space-x-1">
                                                        <span className="text-gray-600">Style:</span>
                                                        {
                                                            listing.release.styles.length > 0 ?
                                                                (
                                                                    listing.release.styles.map(
                                                                        style =>
                                                                            <span
                                                                                key={style.id}
                                                                                className="flex items-center px-2 rounded-md text-xs bg-gray-500 text-white">
                                                                            {style.name}
                                                                        </span>
                                                                    )
                                                                )
                                                                : <span className="text-gray-200">N/A</span>
                                                        }
                                                    </div>

                                                    <div className="flex space-x-1">
                                                        <span className="text-gray-600">Formats:</span>
                                                        {
                                                            listing.release.formats.length > 0 ?
                                                                (
                                                                    listing.release.formats.map(
                                                                        format =>
                                                                            <span
                                                                                key={format.id}
                                                                                className="flex items-center px-2 rounded-md text-xs bg-gray-300 text-gray-500">
                                                                            {format.name}
                                                                        </span>
                                                                    )
                                                                )
                                                                : <span className="text-gray-200">N/A</span>
                                                        }
                                                    </div>


                                                </div>
                                            </td>

                                            <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                                <ReleaseStats release={listing.release}/>
                                                <ul className="flex items-center space-x-2">
                                                    <li>
                                                        <a target="_blank"
                                                           href={`https://www.discogs.com/sell/item/${listing.discogs_id}`}
                                                           className="text-xs flex items-center space-x-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" strokeWidth={1.5}
                                                                 stroke="currentColor" className="size-4">
                                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                                      d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                                                            </svg>

                                                            <span>Listing</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a target="_blank"
                                                           href={`https://www.discogs.com/release/${listing.release.discogs_id}`}
                                                           className="text-xs flex items-center space-x-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" strokeWidth={1.5}
                                                                 stroke="currentColor" className="size-4">
                                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                                      d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                                            </svg>


                                                            <span>Release</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                            <td className="px-6 py-4">
                                                <PriceDisplay value={listing.price_value}
                                                              currency={listing.price_currency}/>
                                            </td>
                                            <td className="px-6 py-4 space-y-1">
                                                <div className="flex flex-col space-y-2 flex-nowrap">
                                                    <div className="flex items-center space-x-2  flex-nowrap">
                                                        <FaRecordVinyl/>
                                                        <ConditionBadge condition={listing.media_condition}/>
                                                    </div>

                                                    <div className="flex items-center space-x-2">
                                                        <FaRegSquare/>
                                                        <ConditionBadge condition={listing.sleeve_condition}/>
                                                    </div>
                                                </div>


                                            </td>

                                        </tr>
                                        )}
                                    )}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="mt-4 flex items-center justify-center mx-auto">
                            <Pagination links={listings.links}/>
                        </div>
                    </div>
                </div>
            </div>
</AuthenticatedLayout>
)
    ;
}
