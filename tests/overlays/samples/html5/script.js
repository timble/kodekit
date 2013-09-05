var elements = document.querySelectorAll('#section h2');
for (key in elements)
{
    if(elements.hasOwnProperty(key)) break;

    elements[key].style.position = 'absolute';
}
