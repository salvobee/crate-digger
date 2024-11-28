export default function LabelBadge({ label, catalogNumber })
{
    return (
        <div className="flex items-center space-x-2">
            <span className="font-light text-gray-500 dark:text-gray-200">{label}</span>
            <span className="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">{catalogNumber}</span>
        </div>
    )
};
