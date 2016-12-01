function handleDragStart(e) {
  this.style.opacity = '0.4';  // this / e.target is the source node.
}

var cols = document.querySelectorAll('#draggable-columns .draggable-column');
[].forEach.call(cols, function(col) {
  col.addEventListener('dragstart', handleDragStart, false);
});
