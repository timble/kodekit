var elements = document.querySelectorAll('#section a');
for (key in elements)
{
    if(elements.hasOwnProperty(key)) break;

    elements[key].style.position = 'absolute';
}
