export default function TracksList({ release }) {
    return (
        <div className="overflow-x-auto">
            <h3 className="my-2">Track List</h3>
            <ul className="w-full divide-y divide-gray-200 border border-gray-300">
                {release.tracks_list.map((track, index) => (
                    <li
                        key={index}
                        className={`p-4 flex flex-col sm:flex-row sm:items-center ${
                            index % 2 === 0 ? 'bg-gray-700' : 'bg-gray-600'
                        }`}
                    >
                        <div className="w-16 text-white font-medium">{track.position}</div>
                        <div className="flex-1">
                            <div className="text-gray-300 font-semibold">{track.title}</div>
                            {track.extraartists && (
                                <div className="text-sm text-gray-400 mt-1">
                                    {track.extraartists.map((artist, i) => (
                                        <div key={i}>
                                            <span className="font-bold">{artist.role}</span>: {artist.name}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="w-20 text-right text-gray-400">{track.duration}</div>
                    </li>
                ))}
            </ul>
        </div>
    );
}

