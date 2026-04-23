function decoratorListener(event)
{
    var target = event.target;
    if (! target)
    {
        return;
    }

    // Read the full path of the actioned element.
    var path = event.composedPath();

    // Bubble up through the parent nodes. 
    for (var i = 0; i < path.length; i++)
    {
        // Make sure the node is an element node, such as <div> or <input>, and that it has attributes set.
        if ((path[i].nodeType === Node.ELEMENT_NODE) && path[i].hasAttributes())
        {
            // Read the decorator from the element.
            var method = path[i].getAttribute('data-decorator');
            var object = path[i].getAttribute('data-object');

            // Check for a function matching the method from the element.
            if ((method !== null) && (
                    (typeof window[method] === 'function') || ((object !== null) && (typeof window[object][method] === 'function'))
                ))
            {
                // Action to respond to
                var trigger = path[i].getAttribute('data-trigger');

                // Check the action against the event, if an action exists on the element.
                if ((trigger === null) || (trigger === event.type))
                {
                    // Trigger the function before exiting.
                    if ((object !== null) && (typeof window[object][method] === 'function'))
                    {
                        window[object][method](path[i], event);
                    }
                    else 
                    {
                        window[method](path[i], event);
                    }
                }

                // Do not search for any other possible actions.
                break;
            }
        }
    }
}
