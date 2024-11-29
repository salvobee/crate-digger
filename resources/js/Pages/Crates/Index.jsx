import { useState } from "react";
import {Head, router} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import LabelBadge from "@/Components/LabelBadge.jsx";
import {FaRecordVinyl} from "react-icons/fa";
import ReleaseStats from "@/Components/ReleaseStats.jsx";
import PriceDisplay from "@/Components/PriceDisplay.jsx";

export default function Index({ crates, selected }) {
    // Raggruppa i crates per negozio
    const groupedCrates = crates.reduce((acc, crate) => {
        const key = crate.listing.inventory.seller_username;
        if (!acc[key]) acc[key] = [];
        acc[key].push(crate);
        return acc;
    }, {});

    const initialVideo = selected ?
        crates.filter(crate => crate.id = selected)[0].listing.release.videos[0]
        : null

    // Stato per il video selezionato
    const [selectedVideo, setSelectedVideo] = useState(initialVideo);
    const [videoList, setVideoList] = useState([]);

    const handleVideoChange = (videos) => {
        setVideoList(videos);
        setSelectedVideo(videos[0] || null); // Imposta il primo video disponibile
    };

    const clearPlayer = () => {
        setSelectedVideo(null)
        setVideoList([])
    }

    function isCratePlaying(crate) {
        return crate.listing.release.videos.filter(video => video.uri === selectedVideo.uri).length > 0;
    }

    const handleRemoveCrate = (crate) => {
        if (selectedVideo && isCratePlaying(crate))
            clearPlayer()
        router.delete(`/crates/${crate.id}`)
    };
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Dig your crates!
                </h2>
            }
        >
            <Head title={`Dig your Crates!`} />

            <span className="bg-blue-500"></span>
            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow sm:rounded-lg dark:bg-gray-800">
                        <div className="p-4 sm:p-8">
                            <div className="flex space-x-4">
                                {/* Lista di negozi */}
                                <div className="w-3/5 space-y-6">
                                    {Object.entries(groupedCrates).map(([seller, crates]) => (
                                        <div key={seller} className="bg-gray-100 p-4 rounded-lg dark:bg-gray-700">
                                            <h3 className="text-lg font-bold text-gray-900 dark:text-gray-200">
                                                Store: {seller}
                                            </h3>
                                            <ul className="mt-4 space-y-2">
                                                {crates.map((crate) => (
                                                    <li
                                                        key={crate.id}
                                                        className="flex flex-col pb-8 border-b"
                                                    >
                                                        <div className="flex space-x-4">
                                                            <button
                                                                className={`
                                                                ${selectedVideo && isCratePlaying(crate)
                                                                    ? "bg-gray-800 text-white"
                                                                    : "hover:bg-gray-200 dark:hover:bg-gray-600"
                                                                }
                                                                w-full px-2 rounded-md text-left
                                                                flex space-x-2 items-center
                                                            `}
                                                                onClick={() => handleVideoChange(crate.listing.release.videos)}
                                                            >
                                                                <FaRecordVinyl/>
                                                                <span>{`${crate.listing.release.artist} - ${crate.listing.release.title}`}</span>
                                                            </button>
                                                            <span>
                                                                <PriceDisplay value={crate.listing.price_value} currency={crate.listing.price_currency} />
                                                            </span>
                                                            <button
                                                                onClick={() => handleRemoveCrate(crate)}
                                                                className='border border-red-500 rounded-md text-red-500 px-2 py-1'>
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                     viewBox="0 0 24 24" strokeWidth={1.5}
                                                                     stroke="currentColor" className="size-6">
                                                                    <path strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <div className="mt-2 ml-2 flex items-center space-x-2">
                                                            <span
                                                                className="font-bold">{crate.listing.release.year}</span>
                                                            <span>/</span>
                                                            <LabelBadge label={crate.listing.release.label}
                                                                        catalogNumber={crate.listing.release.catalog_number}/>
                                                        </div>


                                                        <div className="ml-2 mt-4 flex space-x-4">

                                                            <ReleaseStats layout="row" release={crate.listing.release}/>

                                                            <ul className="flex items-center space-x-2">
                                                                <li>
                                                                    <a target="_blank"
                                                                       href={`https://www.discogs.com/sell/item/${crate.listing.discogs_id}`}
                                                                       className="text-xs flex items-center space-x-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             fill="none"
                                                                             viewBox="0 0 24 24" strokeWidth={1.5}
                                                                             stroke="currentColor" className="size-4">
                                                                            <path strokeLinecap="round"
                                                                                  strokeLinejoin="round"
                                                                                  d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                                                                        </svg>

                                                                        <span>Listing</span>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a target="_blank"
                                                                       href={`https://www.discogs.com/release/${crate.listing.release.discogs_id}`}
                                                                       className="text-xs flex items-center space-x-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             fill="none"
                                                                             viewBox="0 0 24 24" strokeWidth={1.5}
                                                                             stroke="currentColor" className="size-4">
                                                                            <path strokeLinecap="round"
                                                                                  strokeLinejoin="round"
                                                                                  d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                                                        </svg>


                                                                        <span>Release</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div
                                                            className="ml-2 mt-4 flex flex-col space-y-2 max-w-md overflow-hidden">
                                                            <div className="flex space-x-1">
                                                                <span className="text-gray-600">Genre:</span>
                                                                {
                                                                    crate.listing.release.genres.length > 0 ?
                                                                        (
                                                                            crate.listing.release.genres.map(
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
                                                                    crate.listing.release.styles.length > 0 ?
                                                                        (
                                                                            crate.listing.release.styles.map(
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
                                                                    crate.listing.release.formats.length > 0 ?
                                                                        (
                                                                            crate.listing.release.formats.map(
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
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    ))}
                                </div>

                                {/* Video Player */}
                                <div className="sticky absolute top-4 w-2/5 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 shadow">
                                    {selectedVideo ? (
                                        <div>
                                            <iframe
                                                width="100%"
                                                height="315"
                                                src={`${selectedVideo.uri.replace("watch?v=", "embed/")}?autoplay=1`}
                                                title="YouTube video player"
                                                frameBorder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowFullScreen
                                            ></iframe>
                                            <div className="mt-4">
                                                <h4 className="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                                    More Videos:
                                                </h4>
                                                <ul className="mt-2 space-y-2">
                                                    {videoList.map((video, index) => (
                                                        <li
                                                            key={index}
                                                            className={`cursor-pointer p-2 rounded ${
                                                                video.uri === selectedVideo.uri
                                                                    ? "bg-gray-800 text-white"
                                                                    : "hover:bg-gray-200 dark:hover:bg-gray-600"
                                                            }`}
                                                            onClick={() => setSelectedVideo(video)}
                                                        >
                                                            {video.title || `Video ${index + 1}`}
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className="text-gray-500 dark:text-gray-400">
                                            Select a track to play its videos.
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
