import { FaStar, FaStarHalfAlt, FaRegStar } from 'react-icons/fa';

export default function StarRating({ release }) {

    const {rating_average, rating_count} = release

    if (rating_average === null) return <span className="text-gray-500">Rating: N/A</span>;

    // Determina quante stelle piene, mezze e vuote disegnare
    const fullStars = Math.floor(rating_average); // Stelle piene
    const halfStar = rating_average % 1 >= 0.5; // Mezza stella
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0); // Stelle vuote

    return (
        <div className="flex flex-col space-y-2">
            <div className="flex items-center space-x-2">
                <span className="text-lg bg-gray-200 px-2 py-1 rounded-md">
                    {rating_average}
                </span>
                <div className="flex flex-col space-y-1">

                    <div className="flex items-center space-x-1">
                        {Array(fullStars).fill(<FaStar className="text-yellow-500"/>)}
                        {halfStar && <FaStarHalfAlt className="text-yellow-500"/>}
                        {Array(emptyStars).fill(<FaRegStar className="text-yellow-500"/>)}
                    </div>
                    <p className="text-xs text-nowrap text-gray-500">{rating_count} reviews</p>
                </div>
            </div>


        </div>

    );
}
