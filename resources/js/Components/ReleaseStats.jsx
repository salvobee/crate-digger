import StarRating from "@/Components/StarRating.jsx";

export default function ReleaseStats({release}) {

    const {want, have} = release

    function getClassName(value, type) {
        // Determina la classe Tailwind in base al valore e al tipo (red o green)
        const colorBase = type === 'want' ? 'bg-red-' : 'bg-green-';
        if (value <= 10) return colorBase + '50';
        if (value <= 30) return colorBase + '100';
        if (value <= 100) return colorBase + '200';
        if (value <= 300) return colorBase + '300';
        if (value <= 500) return colorBase + '400';
        if (value <= 700) return colorBase + '500';
        if (value <= 900) return colorBase + '600';
        if (value <= 1000) return colorBase + '700';
        return colorBase + '900';
    }

    return (
        <div className="flex flex-col space-y-1">
            {/* Wants */}
            <div className="flex items-center space-x-2">
                <span
                    className={`w-4 h-4 flex ${getClassName(want, 'want')}`}
                ></span>
                <span className='text-xs text-nowrap'>{want} wants</span>
            </div>

            <div className="flex items-center space-x-2">
                <span
                    className={`w-4 h-4 flex ${getClassName(have, 'have')}`}
                ></span>
                <span className='text-xs  text-nowrap'>{have} have</span>
            </div>

            <div className="pt-2">
                <StarRating release={release}/>
            </div>
        </div>
    )
        ;
}
