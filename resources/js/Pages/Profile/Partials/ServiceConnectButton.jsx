export default function ServiceConnectButton({ className = '' }) {

    return (
        <section className={`space-y-6 ${className}`}>
            <p className="text-center py-10">
                Seems that you still not connected any Discogs account to your profile
            </p>

            <div className="flex items-center justify-center">
                <a className="flex items-center border border-white bg-black text-white rounded-md px-4 py-2" href={route('discogs.create')}>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                         stroke="currentColor" className="size-6 mr-2">
                        <path strokeLinecap="round" strokeLinejoin="round"
                              d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Connect with
                    <img className="size-16 pl-2" alt="Discogs"
                         src="https://upload.wikimedia.org/wikipedia/commons/f/ff/Discogs_logo_white.svg"/>
                </a>
            </div>
        </section>
    );
}
