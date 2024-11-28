import {useState} from "react";

export default function Facet({ facet, onChange }) {
    const [isExpanded, setIsExpanded] = useState(false);

    const sortedOptions = [...facet.options].sort((a, b) => b.total - a.total);
    const visibleOptions = sortedOptions.slice(0, 5);
    const hiddenOptions = sortedOptions.slice(5);

    return (
        <div className="flex flex-col space-y-2">
            <h3 className="font-bold">{facet.title}</h3>
            <ul className="space-y-1">
                {visibleOptions.map(option => (
                    <li key={option.value}>
                        <button
                            onClick={() => onChange(facet.fieldname, option.value)}
                            className={`text-left ${option.selected ? "font-bold underline" : ""}`}
                        >
                            {option.value} ({option.total})
                        </button>
                    </li>
                ))}
                {hiddenOptions.length > 0 && (
                    <>
                        {isExpanded && (
                            hiddenOptions.map(option => (
                                <li key={option.value}>
                                    <button
                                        onClick={() => onChange(facet.fieldname, option.value)}
                                        className={`text-left ${option.selected ? "font-bold underline" : ""}`}
                                    >
                                        {option.value} ({option.total})
                                    </button>
                                </li>
                            ))
                        )}
                        <button onClick={() => setIsExpanded(!isExpanded)} className="text-sm text-blue-500">
                            {isExpanded ? "Show less" : "Show more"}
                        </button>
                    </>
                )}
            </ul>
        </div>
    );
}
