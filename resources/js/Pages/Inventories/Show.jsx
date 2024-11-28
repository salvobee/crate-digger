import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, usePage} from '@inertiajs/react';
import PriceDisplay from "@/Components/PriceDisplay.jsx";
import ConditionBadge from "@/Components/ConditionBadge.jsx";
import LabelBadge from "@/Components/LabelBadge.jsx";
import Pagination from '@/Components/Pagination';
import {FaRecordVinyl, FaRegSquare} from 'react-icons/fa';
import ReleaseStats from "@/Components/ReleaseStats.jsx";

export default function Show(props) {

    const { listings } = usePage().props
    const { criteria, store } = props;

    const handleSortChange = (event) => {
        const selectedSort = event.target.value;

        // Ottieni i parametri attuali dall'URL
        const params = new URLSearchParams(window.location.search);

        // Aggiorna o aggiungi il parametro `sort`
        params.set('sort', selectedSort);

        // Reindirizza alla nuova URL
        window.location.search = params.toString();
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
                bg-red-50
                bg-red-100
                bg-red-200
                bg-red-300
                bg-red-400
                bg-red-500
                bg-red-600
                bg-red-700
                bg-red-900

                bg-green-50
                bg-green-100
                bg-green-200
                bg-green-300
                bg-green-400
                bg-green-500
                bg-green-600
                bg-green-700
                bg-green-900
            "></span>
            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                        <div className="mb-4 flex items-center justify-between">
                            <h1>
                                <strong>{listings.total}</strong> listings in {store.seller_username}'s Inventory</h1>

                            <div className="flex items-center space-x-2">
                                <span>Sort by</span>
                                <form>
                                    <select name="sort"  onChange={handleSortChange} defaultValue={new URLSearchParams(window.location.search).get('sort') || ''}>
                                        {criteria.map(criteria => (
                                            <option key={criteria.key} value={criteria.key}>{criteria.description}</option>
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
                            {listings.data.map((listing) => (
                                <tr key={listing.id}>
                                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">

                                        <div className="max-w-lg text-lg text-nowrap text-ellipsis  overflow-hidden">
                                            <a target="_blank" title={`${listing.release.artist} - ${listing.release.title}`} href={`https://discogs.com/release/${listing.release.discogs_id}`}>
                                                {listing.release.artist} - {listing.release.title}
                                            </a>
                                        </div>
                                        <div className="mt-4 flex items-center space-x-2">
                                            <span className="font-bold">{listing.release.year}</span>
                                            <span>/</span>
                                            <LabelBadge label={listing.release.label}
                                                        catalogNumber={listing.release.catalog_number}/>
                                        </div>
                                    </td>

                                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                        <ReleaseStats release={listing.release}/>
                                    </td>
                                    <td className="px-6 py-4">
                                        <PriceDisplay value={listing.price_value} currency={listing.price_currency}/>
                                    </td>
                                    <td className="px-6 py-4 space-y-1">
                                        <div className="flex flex-col space-y-2 flex-nowrap">
                                            <div className="flex items-center space-x-2  flex-nowrap">
                                                <FaRecordVinyl/>
                                                <ConditionBadge condition={listing.media_condition}/>
                                            </div>

                                            <div className="flex items-center space-x-2">
                                            <FaRegSquare  />
                                                <ConditionBadge condition={listing.sleeve_condition}/>
                                            </div>
                                        </div>


                                    </td>

                                </tr>
                            ))}
                            </tbody>
                        </table>

                        <div className="mt-4 flex items-center justify-center mx-auto">
                             <Pagination links={listings.links} />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
