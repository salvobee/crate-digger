export default function QuickPaginator({ links }) {
    const previousPageLink = links.length > 0 && links.at(0);
    const nextPageLink = links.length > 1 && links.at(links.length - 1);

    function renderLabel(label) {
        return { __html: label }; // Interpreta HTML entities
    }

    return (
        <div className="flex space-x-4">
            { previousPageLink.url && <a  className={"underline font-bold"} href={previousPageLink.url}  dangerouslySetInnerHTML={renderLabel(previousPageLink.label)} /> }
            { nextPageLink && <a  className={"underline font-bold"}    href={nextPageLink.url}  dangerouslySetInnerHTML={renderLabel(nextPageLink.label)} />}
        </div>
    )
}
