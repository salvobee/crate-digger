import {useState} from "react";

export default function YearRangeSelector({onChange, activeFilters}) {

    const isYearFromActive = activeFilters && Object.hasOwn(activeFilters, 'year_from');
    const isYearToActive = activeFilters && Object.hasOwn(activeFilters, 'year_to');


    const [yearRange, setYearRange] = useState([
        isYearFromActive ? activeFilters.year_from : 1990,
        isYearToActive ? activeFilters.year_to : 1999
    ]);


    const handleYearFromChange = (e) => {
        const newValue = parseInt(e.target.value);
        setYearRange([newValue, yearRange[1]])
        onChange('year_from', newValue)
    }
    const handleYearToChange = (e) => {
        const newValue = parseInt(e.target.value);
        setYearRange([yearRange[0], newValue])
        onChange('year_to', newValue)
    }


    return (
        <div className="flex flex-col space-y-4">
            <h3 className="font-bold text-lg">Filter by Year</h3>


            {/* Slider per il range di anni */}
            <div className="space-y-2">
                <label htmlFor="year" className="text-sm font-medium">Year Range</label>
                <div className="flex space-x-2 justify-between">
                    <input
                        type="range"
                        id="year"
                        min="1950"
                        max={(new Date).getFullYear()} // Ultimo anno della decade
                        step="1"
                        value={yearRange[0]}
                        onChange={handleYearFromChange}
                        className="w-full"
                    />
                    <input
                        type="range"
                        id="yearTo"
                        min="1950"
                        max={(new Date).getFullYear()}
                        step="1"
                        value={yearRange[1]}
                        onChange={handleYearToChange}
                        className="w-full"
                    />
                </div>
                <div className="flex justify-between text-sm">
                    <span>From: {yearRange[0]}</span>
                    <span>To: {yearRange[1]}</span>
                </div>
            </div>
        </div>
    )
}
