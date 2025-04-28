
    document.addEventListener('DOMContentLoaded', function() {
      const navLinks = document.querySelectorAll('.nav-link');
      
      navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
          this.style.opacity = '1';
          this.style.transform = 'translateY(-1px)';
        });
        
        link.addEventListener('mouseleave', function() {
          this.style.opacity = '.8';
          this.style.transform = 'translateY(0)';
        });
      });
    });
  