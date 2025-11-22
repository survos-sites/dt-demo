#!/usr/bin/env bash

# Download datasets to data/ directory
mkdir -p data

echo "Downloading datasets..."

# Books - CORGIS Classics (1000 Project Gutenberg books)
echo "📚 Downloading classics.csv (1000 classic books)..."
wget -O data/classics.csv https://raw.githubusercontent.com/RealTimeWeb/datasets/master/classics/classics.csv

# Books - Goodreads 10k (modern books with ratings)
echo "📚 Downloading goodreads-books.csv (10k modern books)..."
wget -O data/goodreads-books.csv https://raw.githubusercontent.com/zygmuntz/goodbooks-10k/master/books.csv

# Recipes - 13k recipes from Epicurious
echo "🍳 Downloading recipes.csv (13k recipes)..."
wget -O data/recipes.csv https://raw.githubusercontent.com/josephrmartinez/recipe-dataset/main/13k-recipes.csv

echo ""
echo "✅ All datasets downloaded to data/ directory:"
ls -lh data/


#!/usr/bin/env bash

mkdir -p data

echo "📽️  Downloading movie dataset with poster URLs..."
wget -O data/movies.csv.gz https://github.com/metarank/msrd/raw/master/dataset/movies.csv.gz
gunzip data/movies.csv.gz

echo "✅ Downloaded movies.csv with poster_url field!"
echo ""
echo "Sample record:"
head -n 2 data/movies.csv | mlr --itsv --ojson --jvstack cat

echo ""
echo "Quick stats:"
echo "  goodreads-books.csv: $(wc -l < data/goodreads-books.csv) rows"
echo "  recipes.csv: $(wc -l < data/recipes.csv) rows"
echo "  movies.csv: $(wc -l < data/movies.csv) rows"


