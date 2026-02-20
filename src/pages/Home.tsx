import { useState } from "react";
import Header from "@/components/Header";
import DailyLoginBar from "@/components/DailyLoginBar";
import ProductCard from "@/components/ProductCard";
import { useCategories, useLoginStreak, useProducts } from "@/hooks/useCatalog";
import { Skeleton } from "@/components/ui/skeleton";
import { useAuth } from "@/contexts/AuthContext";

// Emoji map — augment API category names with visuals
const CATEGORY_EMOJI: Record<string, string> = {
  "eletrônicos": "📱", "informática": "💻", "moda": "👕", "calçados": "👟",
  "casa e decoração": "🏠", "eletrodomésticos": "🔌", "esportes e lazer": "⚽",
  "beleza e saúde": "💄", "games": "🎮", "alimentos e bebidas": "🍔",
  "livros e papelaria": "📚", "brinquedos": "🧸", "automotivo": "🚗",
  "ferramentas": "🔧", "animais de estimação": "🐾",
};

const currentDayOfWeek = new Date().getDay();

const Home = () => {
  const [selectedCategory, setSelectedCategory] = useState<string | undefined>(undefined);
  const [search, setSearch] = useState("");
  const { isAuthenticated } = useAuth();

  const { data: categoriesRes } = useCategories();
  const { data: productsRes, isLoading: loadingProducts } = useProducts({
    categoryId: selectedCategory,
    q: search || undefined,
    perPage: 12,
  });
  const { data: streakRes } = useLoginStreak(isAuthenticated);

  const categories = categoriesRes?.data ?? [];
  const products = productsRes?.data ?? [];
  const loginDays: boolean[] = streakRes?.data?.weekDays ?? Array(7).fill(false);
  const totalCoins: number = streakRes?.data?.totalCoins ?? 0;

  return (
    <div className="min-h-screen bg-background">
      <Header coinBalance={totalCoins} onSearch={setSearch} />

      {/* Daily Login Gamification */}
      <DailyLoginBar
        loginDays={loginDays}
        currentDay={currentDayOfWeek}
        totalCoins={totalCoins}
        isAuthenticated={isAuthenticated}
      />

      {/* Categories */}
      <div className="bg-card border-b border-border">
        <div className="container py-3">
          <div className="flex gap-2 overflow-x-auto no-scrollbar">
            <button
              onClick={() => setSelectedCategory(undefined)}
              className={`flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                !selectedCategory
                  ? "bg-brand text-primary-foreground"
                  : "bg-secondary hover:bg-brand-light text-foreground"
              }`}
            >
              Todos
            </button>
            {categories.map((category) => {
              const emoji = CATEGORY_EMOJI[category.name.toLowerCase()] ?? "🛍️";
              return (
                <button
                  key={category.id}
                  onClick={() =>
                    setSelectedCategory(
                      selectedCategory === category.id ? undefined : category.id
                    )
                  }
                  className={`flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                    selectedCategory === category.id
                      ? "bg-brand text-primary-foreground"
                      : "bg-secondary hover:bg-brand-light text-foreground"
                  }`}
                >
                  <span className="mr-1.5">{emoji}</span>
                  {category.name}
                </button>
              );
            })}
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
          <h2 className="text-lg font-bold text-foreground">
            {selectedCategory ? "Produtos filtrados" : "Produtos em destaque"}
          </h2>
          {productsRes?.meta && (
            <span className="text-sm text-muted-foreground">
              {productsRes.meta.total} produtos
            </span>
          )}
        </div>

        {loadingProducts ? (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            {Array.from({ length: 8 }).map((_, i) => (
              <Skeleton key={i} className="aspect-square rounded-xl" />
            ))}
          </div>
        ) : products.length === 0 ? (
          <p className="text-center text-muted-foreground py-16">
            Nenhum produto encontrado.
          </p>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            {products.map((product, index) => (
              <div
                key={product.id}
                className="animate-slide-up"
                style={{ animationDelay: `${index * 50}ms` }}
              >
                <ProductCard
                  id={product.id}
                  title={product.name}
                  price={product.price.amount / 100}
                  images={product.images}
                  freeShipping={product.price.amount >= 10000}
                />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default Home;
