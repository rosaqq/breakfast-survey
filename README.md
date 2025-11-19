# breakfast-survey
![PHP 8.0.8+](https://img.shields.io/badge/PHP-8.0.8%2B-blue)
![PostgreSQL 13](https://img.shields.io/badge/PostgreSQL-13-blue)
![uptime](https://img.shields.io/uptimerobot/ratio/m788675576-ab91f32ea57c23f2833351a2)


### Hosted at -> [breakfast.lnav.pt](https://breakfast.sknv.net/)

PHP based website to collect breakfast information and store it in a PostgreSQL DB.  
Data visualization with [CanvasJS](https://canvasjs.com/).  
Frontend built with [Bulma](https://bulma.io/).

### Database configuration:
- `index.php` - line 38
- `data.php` - line 3
```php
new PDO('pgsql:
            host=db;
            port=5432;
            dbname=db;
            user=bkfast;
            password=bekfast');
```
Table will be automatically created if provided with a valid DB connection.

### Todo:
- [x] Prevent SQL injections
- [ ] DB connection in separate file
- [ ] Use PHP `include` for cleaner code
- [ ] Group similar entries (semantiacally - e.g. bread and butter, bread, bread with butter)
- [ ] Group similar entries (capitalization - e.g. Cereal, cereal, etc)
- [ ] Remove non integer ticks from plot
- [ ] Please make the form keep field values after submitting with errors... 

# Model3D
If you happen to find a curious "3D viewer" button...  
...completely unrelated to breakfasts - please don't go - I can explain.  
So I had this friend looking at a maintenance actions table and I wanted to quickly provide a visualization:  
- Red = corrosion
- Blue = fracture
- Green = other
- Yellow = your very own click ✨raytraced✨ to plop a personal blob on the surface of the model

And this was all made before the age of LLMs can you imagine?  
I sure know I couldn't do it on my own now  

### Todo:
- [ ] I actually can't remember how I did the parts mapping: figure it out and document the logic
- [ ] Allow for data input / manipulation
- [ ] Split into separate website