// Full example
  //alert("function start");
  var id_ = 'columns';
  var cols_ = document.querySelectorAll('#columns .column');
  var element=document.getElementById("drag1");
  var dragSrcEl_ = null;
  document.getElementById('tab1').style.display = 'block';
  document.getElementById('tab2').style.display = 'block';
  document.getElementById('tab3').style.display = 'block';

   function handleDragStart(e) {
    //alert("dragstart");
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
    dragSrcEl_ = this;
    // this/e.target is the source node.
    this.classList.add('moving');
  }

  this.handleDragOver = function(e) {
    if (e.preventDefault) {
      e.preventDefault(); // Allows us to drop.
    }

    e.dataTransfer.dropEffect = 'move';

    return false;
  };

  this.handleDragEnter = function(e) {
      //alert("dragenter");
    this.classList.add('over');
  };

  this.handleDragLeave = function(e) {
    // this/e.target is previous target element.
    this.classList.remove('over');
  };

  this.handleDrop = function(e) {
    // this/e.target is current target element.

    if (e.stopPropagation) {
      e.stopPropagation(); // stops the browser from redirecting.
    }

    // Don't do anything if we're dropping on the same column we're dragging.
    if (dragSrcEl_ != this) {
      dragSrcEl_.innerHTML = this.innerHTML;
      this.innerHTML = e.dataTransfer.getData('text/html');

      // Set number of times the column has been moved.
      var count = this.querySelector('.count');
      var newCount = parseInt(count.getAttribute('data-col-moves')) + 1;
      count.setAttribute('data-col-moves', newCount);
      count.textContent = 'moves: ' + newCount;
    }

    return false;
  };

  this.handleDragEnd = function(e) {
    // this/e.target is the source node.
    [].forEach.call(cols_, function (col) {
      col.classList.remove('over');
      col.classList.remove('moving');
    });
  };

  var cols = document.querySelectorAll('#columns .column');

  [].forEach.call(cols, function(col) {
    col.addEventListener('dragstart', handleDragStart, false);
    col.addEventListener('dragenter', this.handleDragEnter, false);
    col.addEventListener('dragover', this.handleDragOver, false);
    col.addEventListener('dragleave', this.handleDragLeave, false);
    col.addEventListener('drop', this.handleDrop, false);
    col.addEventListener('dragend', this.handleDragEnd, false);
  });
