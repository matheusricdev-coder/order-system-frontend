import Header from "@/components/Header";
import ProductCard from "@/components/ProductCard";

const mockProducts = [
  {
    id: 1,
    title: "Smartphone Samsung Galaxy S24 Ultra 256GB 5G",
    price: 5499.00,
    originalPrice: 7299.00,
    image: "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 2,
    title: "Fone de Ouvido Bluetooth JBL Tune 520BT",
    price: 199.90,
    originalPrice: 299.90,
    image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 3,
    title: "Tênis Nike Air Max 90 Masculino",
    price: 599.99,
    image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop",
    freeShipping: false,
  },
  {
    id: 4,
    title: "Notebook Dell Inspiron 15 Intel Core i7 16GB",
    price: 3899.00,
    originalPrice: 4599.00,
    image: "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 5,
    title: "Smartwatch Apple Watch Series 9 GPS 45mm",
    price: 3999.00,
    image: "https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 6,
    title: "Câmera Canon EOS R50 Mirrorless 4K",
    price: 5199.00,
    originalPrice: 5999.00,
    image: "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 7,
    title: "Console PlayStation 5 Slim 1TB",
    price: 3799.00,
    image: "https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=400&fit=crop",
    freeShipping: true,
  },
  {
    id: 8,
    title: "Cadeira Gamer ThunderX3 EC3 Preta",
    price: 899.90,
    originalPrice: 1299.90,
    image: "https://images.unsplash.com/photo-1598550476439-6847785fcea6?w=400&h=400&fit=crop",
    freeShipping: false,
  },
];

const categories = [
  { name: "Tecnologia", emoji: "📱" },
  { name: "Moda", emoji: "👕" },
  { name: "Casa", emoji: "🏠" },
  { name: "Esportes", emoji: "⚽" },
  { name: "Beleza", emoji: "💄" },
  { name: "Games", emoji: "🎮" },
];

const Home = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      
      {/* Categories */}
      <div className="bg-card border-b border-border">
        <div className="container py-3">
          <div className="flex gap-2 overflow-x-auto no-scrollbar">
            {categories.map((category) => (
              <button
                key={category.name}
                className="flex-shrink-0 px-4 py-2 bg-secondary hover:bg-brand-light rounded-full text-sm font-medium text-foreground transition-colors"
              >
                <span className="mr-1.5">{category.emoji}</span>
                {category.name}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Banner */}
      <div className="container py-4">
        <div className="bg-gradient-to-r from-brand to-brand-hover rounded-xl p-6 text-primary-foreground animate-fade-in">
          <h2 className="text-2xl font-bold mb-2">🔥 Super Ofertas</h2>
          <p className="text-primary-foreground/80 mb-4">Até 50% de desconto em produtos selecionados</p>
          <button className="bg-primary-foreground text-brand px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition-opacity">
            Ver ofertas
          </button>
        </div>
      </div>

      {/* Products Grid */}
      <div className="container pb-8">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-bold text-foreground">Produtos em destaque</h2>
          <button className="text-sm text-brand font-medium hover:underline">
            Ver todos
          </button>
        </div>
        
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
          {mockProducts.map((product, index) => (
            <div 
              key={product.id} 
              className="animate-slide-up"
              style={{ animationDelay: `${index * 50}ms` }}
            >
              <ProductCard {...product} />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Home;
